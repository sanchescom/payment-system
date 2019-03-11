<?php

namespace App\Entities;

/**
 * Class Secret
 * @package App\Entities
 */
class Secret
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $hash;


    /**
     * @return Secret
     */
    public static function create()
    {
        $secret = new self();
        $secret->code = str_random(3);
        $secret->hash = self::hash($secret->code);

        return $secret;
    }

    /**
     * @param $code
     * @return string
     */
    public static function hash($code)
    {
        return md5(md5($code));
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
}
