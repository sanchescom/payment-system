<?php

namespace App\Traits;

use App\Exceptions\PrivatePropertyException;

trait PrivateAttributes
{
    /**
     * The attributes that are private.
     *
     * @var array
     */
    protected $private = [];


    /**
     * Overwrite for making private property
     *
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public function __get($key)
    {
        $this->checkPrivate($key);

        return parent::__get($key);
    }


    /**
     * @param $key
     * @param $value
     * @return mixed
     * @throws PrivatePropertyException
     */
    public function __set($key, $value)
    {
        $this->checkPrivate($key);

        return parent::__set($key, $value);
    }


    /**
     * @param $property
     * @throws PrivatePropertyException
     */
    protected function checkPrivate($property)
    {
        if (in_array($property, $this->private)) {
            throw new PrivatePropertyException('The ' . $property . ' property is private');
        }
    }
}
