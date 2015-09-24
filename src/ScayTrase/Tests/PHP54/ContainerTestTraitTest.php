<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-09-23
 * Time: 21:45
 */

namespace ScayTrase\Tests\PHP54;

use ScayTrase\Testing\ContainerTestTrait;
use ScayTrase\Tests\PHP53\FixtureBundle;

/**
 * Class ContainerTestTraitTest
 * @package ScayTrase\Tests\PHP53
 * @requires PHP 5.4
 */
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
