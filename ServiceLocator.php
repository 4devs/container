<?php

namespace FDevs\Container;

use FDevs\Container\Exception\ServiceCircularReferenceException;
use FDevs\Container\Exception\ServiceNotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ServiceLocator implements ContainerInterface
{
    /**
     * @var array|callable[]
     */
    private $factories;

    /**
     * @var array|string[]
     */
    private $loading = [];

    /**
     * @param callable[] $factories
     */
    public function __construct(array $factories = [])
    {
        $this->factories = $factories;
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return isset($this->factories[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw $this->createNotFoundException($id);
        }

        if (isset($this->loading[$id])) {
            $ids = array_values($this->loading);
            $ids = array_slice($this->loading, array_search($id, $ids));
            $ids[] = $id;

            throw new ServiceCircularReferenceException($id, $ids);
        }

        $this->loading[$id] = $id;
        try {
            return $this->factories[$id]();
        } finally {
            unset($this->loading[$id]);
        }
    }

    /**
     * @param string $id
     *
     * @return mixed|null
     */
    public function __invoke($id)
    {
        return isset($this->factories[$id]) ? $this->get($id) : null;
    }

    /**
     * @param string $id
     *
     * @return NotFoundExceptionInterface
     */
    protected function createNotFoundException(string $id): NotFoundExceptionInterface
    {
        return new ServiceNotFoundException($id, end($this->loading) ?: null, array_keys($this->factories));
    }
}
