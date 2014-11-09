<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-11-09
 * Time: 15:25
 */

namespace ScayTrase\Tests;

use ScayTrase\Testing\FixtureTestCase;

class FixtureTestCaseTest extends FixtureTestCase
{
    public static function getKernel()
    {
        return new SampleKernel('test',true);
    }

    /**
     * @dataset ScayTrase\Tests\SampleFixture
     * @dataset ScayTrase\Tests\SampleFixture
     */
    public function testFixtureLoading(){
        $this->assertCount(2,$this->getFixtures());
    }
}
 