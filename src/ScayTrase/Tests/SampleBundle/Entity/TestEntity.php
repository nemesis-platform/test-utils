<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-11-09
 * Time: 16:31
 */

namespace ScayTrase\Tests\SampleBundle\Entity;


class TestEntity
{
    /** @var  int|null */
    private $id;
    /** @var  mixed */
    private $field;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }
} 