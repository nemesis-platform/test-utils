<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-11-22
 * Time: 23:26
 */

namespace ScayTrase\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Driver\SahiDriver;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\Symfony2Extension\Driver\KernelDriver;
use PHPUnit_Framework_Assert;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Role\RoleInterface;

class RawSymfonyContext extends RawMinkContext implements KernelAwareContext
{

    use KernelDictionary;

    /**
     * @return Client
     */
    protected function getSymfonyClient()
    {
        /** @var GoutteDriver $driver */
        $driver = $this->getSession('symfony2')->getDriver();

        return $driver->getClient();
    }

    /**
     * @throws UnsupportedDriverActionException
     * @return Profile
     */
    public function getSymfonyProfile()
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

        /** @var $client \Symfony\Bundle\FrameworkBundle\Client */
        $client = $driver->getClient();
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

}
