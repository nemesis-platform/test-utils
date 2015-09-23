<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-09-23
 * Time: 21:45
 */

namespace ScayTrase\Tests;

use ScayTrase\Testing\ContainerTestTrait;

class ContainerTestTraitTest extends \PHPUnit_Framework_TestCase
{
    use ContainerTestTrait;

    public function testContainer()
    {
        $container = $this->buildContainer(array(new FixtureBundle()));
        self::assertTrue($container->hasParameter('fixture.bundle'));
        self::assertTrue($container->getParameter('fixture.bundle'));
    }
}