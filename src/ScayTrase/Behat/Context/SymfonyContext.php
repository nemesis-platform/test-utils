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

class SymfonyContext extends RawMinkContext implements KernelAwareContext
{

    use KernelDictionary;

    /**
     * @Given /^I submit form with button "([^"]*)"$/
     * @param $button
     * @param TableNode $fields
     */
    public function iSubmitForm($button, TableNode $fields)
    {

        $data = array();

        foreach ($fields->getRowsHash() as $field => $value) {
            $data[$field] = $value;
        }

        $driver = $this->getSession()->getDriver();

        if ($driver instanceof SahiDriver) {
            foreach ($data as $key => $value) {
                $js = "document.getElementsByName('$key')[0].value='$value'";
                $this->getSession()->executeScript($js);
            }

            $button = $this->fixStepArgument($button);
            $this->getSession()->getPage()->pressButton($button);

            return;
        }

        if ($driver instanceof KernelDriver) {
            /** @var Client $client */
            $client = $driver->getClient();
            $form = $client->getCrawler()->selectButton($button)->form($data);
            $client->submit($form);
        };


    }


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

    /**
     * @Given /^I logged in as "([^"]*)"$/
     * @When /^I log in as "([^"]*)"$/
     * @param $email
     */
    public function loginAs($email)
    {
        $this->logIn($email);
    }

    /**
     * @param $email
     * @param RoleInterface[] $roles
     */
    protected function logIn($email, $roles = array('ROLE_USER'))
    {
        $container = $this->getContainer();
        $session = $container->get('session');

        $user = $container->get('doctrine.orm.entity_manager')->getRepository(
            'SiteBundle:User'
        )->findOneBy(array('email' => $email));

        PHPUnit_Framework_Assert::assertNotNull($user, 'Unknown user with email ' . $email);

        $firewall = 'secured_area';
        $token = new UsernamePasswordToken($user, null, $firewall, $roles);
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();


        $this->getSession()->setCookie($session->getName(), $session->getId());
    }

    /**
     * @param $email
     * @param TableNode $roles
     * @When /^I log in as "([^"]*)" with roles:$/
     * @Given /^I logged in as "([^"]*)" with roles:$/
     */
    public function loginWithRoles($email, TableNode $roles = null)
    {
        $data = array('ROLE_USER');

        if ($roles) {
            foreach ($roles->getHash() as $field => $value) {
                $data[] = $value;
            }
        }

        $this->logIn($email, $data);
    }


    /**
     * Returns fixed step argument (with \\" replaced back to ").
     *
     * @param string $argument
     *
     * @return string
     */
    protected function fixStepArgument($argument)
    {
        return str_replace('\\"', '"', $argument);
    }
}
