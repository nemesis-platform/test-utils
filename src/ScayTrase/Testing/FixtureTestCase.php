<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 4/18/14
 * Time: 12:03 AM
 */

namespace ScayTrase\Testing;


use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


abstract class FixtureTestCase extends WebTestCase
{
    /** @var  EntityManager */
    protected static $em;
    /** @var  Client */
    protected static $client;

    /** @var  FixtureInterface[] */
    private $fixtures = array();

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return static::$kernel->getContainer();
    }

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::bootKernel();

        $metadata = static::getMetadata();

        $tool = new SchemaTool(static::$em);
        $tool->dropDatabase();
        $tool->createSchema($metadata);

        $validator = new SchemaValidator(static::$em);
        $errors = $validator->validateMapping();

        static::assertCount(
            0,
            $errors,
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

    public static function getMetadata()
    {
        /** @var EntityManagerInterface $em */
        static::$em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $metadata = static::$em->getMetadataFactory()->getAllMetadata();

        return $metadata;
    }

    public function setUp()
    {
        parent::setUp();
        parent::bootKernel();

        $this->fixtures = array();
        $annotations = $this->getAnnotations();

        if (isset($annotations['method']['dataset'])) {
            $dataset_classes = $annotations['method']['dataset'];
            foreach ($dataset_classes as $dataset_class) {
                $fixture = new $dataset_class();
                if (!($fixture instanceof FixtureInterface)) {
                    continue;
                }
                $this->fixtures[] = $fixture;
            }
        }

        static::assertNotNull(static::$kernel->getContainer());

        $this->loadTestData($this->fixtures);
    }

    /**
     * @param FixtureInterface|FixtureInterface[] $data
     */
    private function loadTestData($data)
    {
        $loader = new ContainerAwareLoader(static::$kernel->getContainer());

        if (!is_array($data)) {
            $data = array($data);
        }

        foreach ($data as $dataSet) {
            $loader->addFixture($dataSet);
        }

        $this->fixtures = $loader->getFixtures();

        $purger = new ORMPurger();
        $executor = new ORMExecutor(
            static::$em,
            $purger
        );

        $executor->execute($loader->getFixtures());
    }

    /**
     * @return FixtureInterface[]
     */
    public function getFixtures()
    {
        return $this->fixtures;
    }
}