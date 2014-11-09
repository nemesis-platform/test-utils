<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-11-09
 * Time: 15:25
 */

namespace ScayTrase\Tests;

use ScayTrase\Testing\FixtureTestCase;
use ScayTrase\Testing\KernelForTest;

class FixtureTestCaseTest extends FixtureTestCase
{
    public static function createKernel(array $options = array())
    {
        if (!class_exists('PDO') || !in_array('sqlite', \PDO::getAvailableDrivers())) {
            self::markTestSkipped('This test requires SQLite support in your environment');
        }

        return new KernelForTest('test', true);
    }

    /**
     * @dataset ScayTrase\Tests\SampleFixture
     * @dataset ScayTrase\Tests\SampleFixture
     */
    public function testFixtureLoading()
    {
        $this->assertCount(2, $this->getFixtures());
    }

    public function testNoFixturesLoaded()
    {
        $this->assertEmpty($this->getFixtures());
    }
}
 