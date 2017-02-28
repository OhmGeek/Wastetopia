<?php

/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 28/02/17
 * Time: 14:59
 */
namespace Wastetopia\Config;

use Wastetopia\Config\AbstractConfig;
class ProductionConfig extends AbstractConfig
{
    public function getConfiguration()
    {
        return array(
            'DB_HOST' => 'mysql.dur.ac.uk',
            'DB_NAME' => 'Idcs8s04_Wastetopia',
            'DB_USER' => 'dcs8s04',
            'DB_PASS' => 'when58',

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