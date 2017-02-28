<?php

/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 28/02/17
 * Time: 15:10
 */
class LocalConfig extends AbstractConfig
{

    public function getConfiguration()
    {
        return array(
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'Idcs8s04_Wastetopia',
            'DB_USER' => 'root',
            'DB_PASS' => 'root',

            'TOKEN_BEFORESALT' => 'Dr.Pr0jectWA5t0Pia',
            'TOKEN_AFTERSALT' => 'EndSalt11!!!1',

            'COOKIE_IDENTIFIER' => 'gpwastetopiadata',

            'ROOT_JS' => '',
            'ROOT_CSS' => '',
            'ROOT_IMG' => '',
            'ROOT_BASE' => ''
        );
    }
}