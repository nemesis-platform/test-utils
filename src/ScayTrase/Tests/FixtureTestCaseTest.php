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

    public function sampleDataProvider()
    {
        return array(
            'sample set 1' => array(1,2,3),
            'sample set 2' => array(4,5,9),
        );
    }

    /**
     * @dataset ScayTrase\Tests\SampleFixture
     * @dataProvider sampleDataProvider
     * @param $a
     * @param $b
     * @param $c
     */
    public function testDataProviders($a,$b,$c)
    {
        $this->assertEquals($c,$a+$b);
        $this->assertCount(1,$this->getFixtures());
    }
}
 