<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-11-22
 * Time: 23:26
 */

namespace ScayTrase\Behat\Context;

use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\Symfony2Extension\Driver\KernelDriver;
use Symfony\Bundle\FrameworkBundle\Client as FrameworkClient;
use Symfony\Component\HttpKernel\Client as HttpClient;
use Symfony\Component\HttpKernel\Profiler\Profile;

class RawSymfonyContext extends RawMinkContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @throws UnsupportedDriverActionException
     * @return Profile
     */
    public function getSymfonyProfile()
    {
        /** @var $client FrameworkClient */
        $client = $this->getDriver()->getClient();
        $profile = $client->getProfile();
        if (false === $profile) {
            throw new \RuntimeException(
                'The profiler is disabled. Activate it by setting ' .
                'framework.profiler.only_exceptions to false in ' .
                'your config'
            );
        }

        return $profile;
    }

    /**
     * @return KernelDriver
     * @throws UnsupportedDriverActionException
     */
    public function getDriver()
    {
        $driver = $this->getSession()->getDriver();
        if (!$driver instanceof KernelDriver) {
            throw new UnsupportedDriverActionException(
                'You need to tag the scenario with ' .
                '"@mink:symfony2". Using the profiler is not ' .
                'supported by %s',
                $driver
            );
        }

        return $driver;
    }

    /**
     * @return HttpClient
     */
    protected function getSymfonyClient()
    {
        return $this->getDriver()->getClient();
    }

}
