<?php

namespace FDevs\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;

class ServiceNotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $sourceId;
    /**
     * @var array
     */
    private $alternatives;

    public function __construct(string $id, string $sourceId = null, array $alternatives, \Exception $previous = null)
    {
        $message = sprintf('The service "%s" has a dependency on a non-existent service "%s". This locator %s', $sourceId, $id, $this->formatAlternatives($alternatives));
        parent::__construct($message, 0, $previous);
        $this->id = $id;
        $this->alternatives = $alternatives;
        $this->sourceId = $sourceId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getAlternatives(): array
    {
        return $this->alternatives;
    }

    /**
     * @param array  $alternatives
     * @param string $separator
     *
     * @return string
     */
    private function formatAlternatives(array $alternatives, $separator = 'and')
    {
        $format = '"%s"%s';
        if (empty($alternatives)) {
            return 'is empty...';
        }
        $last = array_pop($alternatives);

        return sprintf($format, $alternatives ? implode('", "', $alternatives) : $last, $alternatives ? sprintf(' %s "%s"', $separator, $last) : '');
    }
}
