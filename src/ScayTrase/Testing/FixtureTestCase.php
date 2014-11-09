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
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;


abstract class FixtureTestCase extends WebTestCase
{
    /** @var  EntityManager */
    protected static $em;
    /** @var  KernelInterface */
    protected static $kernel;
    /** @var  Client */
    protected $client;
    /** @var  FixtureInterface[] */
    private $fixtures = array();

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public static function setUpBeforeClass()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

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

    /**
     * @param array $options
     * @return KernelInterface
     */
    protected static function createKernel(array $options = array())
    {
        return parent::createKernel($options);
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
        $class = get_class($this);
        $method = $this->getName();
        $reflection = new ReflectionMethod($class, $method);
        $doc_block = $reflection->getDocComment();

        // Use regex to parse the doc_block for a specific annotation
        $dataset_classes = static::parseDocBlock($doc_block, '@dataset');

//        if (empty($dataset_classes)) {
//            return;
//        }

        $datasets = array();

        foreach ($dataset_classes as $dataset_class) {
            $datasets[] = new $dataset_class;
        }

        $this->fixtures = $datasets;

        $this->loadTestData($this->fixtures);
    }

    private static function parseDocBlock($doc_block, $tag)
    {

        $matches = array();

        if (empty($doc_block)) {
            return $matches;
        }

        $regex = "/{$tag} (.*)(\\r\\n|\\r|\\n)/U";
        preg_match_all($regex, $doc_block, $matches);

        if (empty($matches[1])) {
            return array();
        }

        // Removed extra index
        $matches = $matches[1];

        // Trim the results, array item by array item
        foreach ($matches as $ix => $match) {
            $matches[$ix] = trim($match);
        }

        return $matches;

    }

    /**
     * @param FixtureInterface|FixtureInterface[] $data
     */
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
    } // parseDocBlock

}