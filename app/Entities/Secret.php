<?php

namespace App\Entities;

/**
 * Class Secret
 *
 * @property string $code
 * @property string $hash
 *
 * @package App\Entities
 */
class Secret
{
    private $code;
    private $hash;


    public static function create()
    {
        $secret = new self();
        $secret->code = str_random(3);
        $secret->hash = self::hash($secret->code);

        return $secret;
    }


    public static function hash($code)
    {
        return md5(md5($code));
    }


    public function getCode()
    {
        return $this->code;
    }


    public function getHash()
    {
        return $this->hash;
    }
}
