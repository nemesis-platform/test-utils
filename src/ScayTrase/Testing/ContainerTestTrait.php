<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-09-20
 * Time: 13:48
 */

namespace ScayTrase\Testing;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

trait ContainerTestTrait
{
    /**
     * @param array $configs
     *
     * @param BundleInterface[] $bundles
     * @return ContainerBuilder
     */
    protected function buildContainer(array $bundles = array(), array $configs = array())
    {
        $container = new ContainerBuilder();

        foreach ($bundles as $bundle) {
            $bundle->build($container);
            $this->loadExtension($bundle, $container, $configs);
        }

        $container->compile();

        $dumper = new PhpDumper($container);
        $dumper->dump();

        return $container;
    }

    /**
     * @param BundleInterface $bundle
     * @param ContainerBuilder $container
     * @param array $configs
     */
    protected function loadExtension(BundleInterface $bundle, ContainerBuilder $container, array $configs)
    {
        $extension = $bundle->getContainerExtension();
        if (!$extension)
            return;

        $config = array_key_exists($extension->getAlias(), $configs) ? $configs[$extension->getAlias()] : array();
        $extension->load(array($config), $container);
    }
}