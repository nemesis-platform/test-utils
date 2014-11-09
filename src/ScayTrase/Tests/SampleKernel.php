<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-11-09
 * Time: 16:34
 */

namespace ScayTrase\Tests;


use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use ScayTrase\Tests\SampleBundle\SampleBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class SampleKernel extends Kernel
{
    /**
     * @return array
     */
    public function registerBundles()
    {
        return array(
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new DoctrineBundle(),
            new DoctrineFixturesBundle(),
            new SensioFrameworkExtraBundle(),
            new SampleBundle()
        );
    }

    public function getCacheDir()
    {
        return __DIR__.'/../../../app/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return __DIR__.'/../../../app/logs/';
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
        $loader->load(__DIR__.'/config.yml');
    }
}