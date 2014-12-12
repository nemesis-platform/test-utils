<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 12.12.2014
 * Time: 11:57
 */

namespace ScayTrase\Behat\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Driver\KernelDriver;
use PHPUnit_Framework_Assert;
use Symfony\Component\HttpKernel\Client;

class MinkContext extends RawMinkContext
{

    /**
     * @Then /^text "([^"]*)" should be visible$/
     * @param $text
     */
    public function textShouldBeVisible($text)
    {
        $this->assertSession()->pageTextContains($this->fixStepArgument($text));
        $node = $this->getSession()->getPage()->find('xpath', "//*[contains(text(),'$text')]");
        PHPUnit_Framework_Assert::assertNotNull($node);

        PHPUnit_Framework_Assert::assertTrue(
            $node->isVisible()
        );
    }

    /**
     * @Then /^text "([^"]*)" should not be visible$/
     * @param $text
     */
    public function textShouldNotBeVisible($text)
    {
        $this->assertSession()->pageTextContains($this->fixStepArgument($text));
        $node = $this->getSession()->getPage()->find('xpath', "//*[contains(text(),'$text')]");
        PHPUnit_Framework_Assert::assertNotNull($node);
        PHPUnit_Framework_Assert::assertFalse(
            $node->isVisible()
        );
    }

    /** Click on the element with the provided xpath query
     *
     * @When /^(?:|I )click on the element "([^"]*)"$/
     * @param $locator
     */
    public function iClickOnTheElement($locator)
    {
        $session = $this->getSession(); // get the mink session
        $element = $session->getPage()->find('css', $locator); // runs the actual query and returns the element

        // errors must not pass silently
        PHPUnit_Framework_Assert::assertNotNull($element, sprintf('Could not evaluate CSS selector: "%s"', $locator));

        $this->getSession()->executeScript(
            "$('#" . $element->getAttribute('id') . "').click()"
        );

        // ok, let's click on it
        $element->click();
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


    /**
     * @Given /^I follow redirection$/
     * @Then /^I should be redirected$/
     */
    public function iFollowRedirection()
    {
        $driver = $this->getSession()->getDriver();
        if ($driver instanceof KernelDriver) {
            /** @var Client $client */
            $client = $driver->getClient();

            $client->followRedirect();
        }
    }

    /**
     * @Given /^auto redirection (enabled|disabled)$/
     * @param $enabled
     */
    public function toggleAutoRedirection($enabled)
    {
        $driver = $this->getSession()->getDriver();
        if ($driver instanceof KernelDriver) {
            /** @var Client $client */
            $client = $driver->getClient();

            if ($enabled === 'enabled') {
                $client->followRedirects(true);
            } else {
                $client->followRedirects(false);

            }
        }
    }
}
