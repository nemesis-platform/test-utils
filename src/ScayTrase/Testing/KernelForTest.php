<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-11-09
 * Time: 16:34
 */

namespace ScayTrase\Testing;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

class KernelForTest extends Kernel
{
    /** @var Bundle[] */
    private $additional_bundles = array();

    private $additional_configs = array();

    /**
     * @param string $environment
     * @param bool $debug
     * @param Bundle[] $additional_bundles
     * @param array $additional_configs
     */

    public function __construct($environment, $debug, $additional_bundles = array(), $additional_configs = array())
    {
        $this->additional_bundles = $additional_bundles;
        $this->additional_configs = array_merge(
            array(
                __DIR__ . '/config.yml',
            ),
            $additional_configs
        );
        parent::__construct($environment, $debug);
    }

    /**
     * @return array
     */
    public function registerBundles()
    {
        return array_merge(
            array(
                new FrameworkBundle(),
                new SecurityBundle(),
                new TwigBundle(),
                new DoctrineBundle(),
            ),
            $this->additional_bundles
        );
    }

    public function getCacheDir()
    {
        return __DIR__ . '/../../../app/cache/' . $this->getEnvironment();
    }

    public function getLogDir()
    {
        return __DIR__ . '/../../../app/logs/';
    }


    /**
     * Loads the container configuration.
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     *
     * @api
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        foreach ($this->additional_configs as $config) {
            $loader->load($config);
        }
    }
}