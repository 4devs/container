<?php

namespace FDevs\Container\Compiler;

use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ServiceLocatorPass implements CompilerPassInterface
{
    use ResolveParamTrait;

    /**
     * @var string
     */
    private $tag;
    /**
     * @var string
     */
    private $serviceId;
    /**
     * @var string
     */
    private $attrName;

    /**
     * ServiceLocatorPass constructor.
     *
     * @param string $serviceId
     * @param string $tag
     * @param string $attrName
     */
    public function __construct(string $serviceId, string $tag, $attrName = 'id')
    {
        $this->tag = $tag;
        $this->serviceId = $serviceId;
        $this->attrName = $attrName;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->serviceId)) {
            return;
        }
        $factories = [];
        foreach ($container->findTaggedServiceIds($this->tag) as $serviceId => $tags) {
            $serviceArgument = new ServiceClosureArgument(new Reference($serviceId));
            foreach ($tags as $taggedAttr) {
                $factories[$this->resolveServiceId($container, $taggedAttr) ?? $serviceId] = $serviceArgument;
            }
        }
        $container
            ->getDefinition($this->serviceId)
            ->setArgument('$factories', $factories)
        ;
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $attr
     *
     * @return null|string
     */
    protected function resolveServiceId(ContainerBuilder $container, array $attr): ?string
    {
        return isset($attr[$this->attrName]) ? $this->resolveParam($container, $attr[$this->attrName]) : null;
    }
}
