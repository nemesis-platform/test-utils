<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-09-20
 * Time: 13:53
 */

namespace ScayTrase\Tests\PHP54;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FixtureBundleExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $config An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $container->setParameter('fixture.bundle', true);
    }
}
