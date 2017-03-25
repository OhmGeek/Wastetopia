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
        // get the database url from the environment variables
        $db_url = urldecode($_ENV['CLEARDB_DATABASE_URL']);

        // return the configuration
        return array(
            'DB_HOST' => $db_url['host'],
            'DB_NAME' => substr($db_url,1),
            'DB_USER' => $db_url['user'],
            'DB_PASS' => $db_url['pass'],
            'TOKEN_BEFORESALT' => 'Dr.Pr0jectWA5t0Pia',
            'TOKEN_AFTERSALT' => 'EndSalt11!!!1',
            'COOKIE_IDENTIFIER' => 'gpwastetopiadata',
            'ROOT_JS' => 'http://wastetopia.herokuapp.com/js',
            'ROOT_CSS' => 'http://wastetopia.herokuapp.com/css',
            'ROOT_IMG' => 'http://wastetopia.herokuapp.com/img',
            'ROOT_BASE' => 'http://wastetopia.herokuapp.com'
        );
    }
}
