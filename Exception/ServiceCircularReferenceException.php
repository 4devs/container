<?php
/*
* This file is part of the Rambler AdTech package.
*
* (c) Andrey Samusev <a.samusev@rambler-co.ru>
*/

namespace FDevs\Container\Exception;

class ServiceCircularReferenceException extends RuntimeException
{
    /**
     * @var string
     */
    private $serviceId;
    /**
     * @var array
     */
    private $path;

    /**
     * ServiceCircularReferenceException constructor.
     *
     * @param string          $serviceId
     * @param array           $path
     * @param \Exception|null $previous
     */
    public function __construct(string $serviceId, array $path, \Exception $previous = null)
    {
        parent::__construct(sprintf('Circular reference detected for service "%s", path: "%s".', $serviceId, implode(' -> ', $path)), 0, $previous);

        $this->serviceId = $serviceId;
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getServiceId(): string
    {
        return $this->serviceId;
    }

    /**
     * @return array
     */
    public function getPath(): array
    {
        return $this->path;
    }
}
