<?php

namespace FDevs\Container\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait ResolveParamTrait
{
    /**
     * @param ContainerBuilder $container
     * @param string           $param
     *
     * @return string
     */
    private function resolveParam(ContainerBuilder $container, string $param): string
    {
        $paramName = \trim($param, '%');

        return $container->hasParameter($paramName) ? $container->getParameter($paramName) : $param;
    }
}
