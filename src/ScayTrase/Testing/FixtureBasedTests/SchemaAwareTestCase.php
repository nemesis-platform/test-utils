<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 4/18/14
 * Time: 12:03 AM
 */

namespace ScayTrase\Testing\FixtureBasedTests;

use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SchemaAwareTestCase extends WebTestCase
{
    /** @var  Client */
    protected $client;

    public static function setUpBeforeClass()
    {

        $client = static::createClient();

        $em = $client->getContainer()->get('doctrine.orm.entity_manager');

        $metadata = $em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadata)) {
            $tool = new SchemaTool($em);
        } else {
            throw new SchemaException('No Metadata Classes to process.');
        }


        $tool->dropDatabase();
        $tool->createSchema($metadata);

        $validator = new SchemaValidator($em);
        $errors = $validator->validateMapping();

        self::assertEquals(
            0,
            count($errors),
            implode(
                "\n\n",
                array_map(
                    function ($l) {
                        return implode("\n\n", $l);
                    },
                    $errors
                )
            )
        );


    }

    public function setUp()
    {
        $this->client = static::createClient();
    }



}