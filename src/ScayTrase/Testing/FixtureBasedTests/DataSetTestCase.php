<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 4/13/14
 * Time: 10:44 PM
 */

namespace ScayTrase\Testing\FixtureBasedTests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class DataSetTestCase extends SchemaAwareTestCase
{
    protected function loadTestData($data)
    {
        $loader = new Loader();

        if (!is_array($data)) {
            $data = array($data);
        }

        foreach ($data as $dataSet) {
            $loader->addFixture($dataSet);
        }

        $purger = new ORMPurger();
        $executor = new ORMExecutor(
            $this->client->getContainer()->get('doctrine.orm.entity_manager'),
            $purger
        );
        $executor->execute($loader->getFixtures());
    }
}