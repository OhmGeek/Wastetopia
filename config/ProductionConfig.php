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
        $db_url = getenv('CLEARDB_DATABASE_URL');
        $comp_url = parse_url($db_url);
        // return the configuration
        return array(
            'DB_HOST' => $comp_url['host'],
            'DB_NAME' => substr($comp_url['path'],1),
            'DB_USER' => $comp_url['user'],
            'DB_PASS' => $comp_url['pass'],
            'TOKEN_BEFORESALT' => 'Dr.Pr0jectWA5t0Pia',
            'TOKEN_AFTERSALT' => 'EndSalt11!!!1',
            'COOKIE_IDENTIFIER' => 'gpwastetopiadata',


            'ROOT_JS' => '//wastetopia.herokuapp.com/js',
            'ROOT_CSS' => '//wastetopia.herokuapp.com/css',

            'ROOT_IMG' => '//wastetopia.herokuapp.com/img',
            'ROOT_BASE' => '//wastetopia.herokuapp.com',

            'EMAIL_HOST' => 'smtp-mail.outlook.com',
            'EMAIL_ADDRESS' => 'wastetopia@outlook.com',
            'EMAIL_PASSWORD' => 'HHrv4673',
            'EMAIL_SECURITY' => 'tls',
            'EMAIL_PORT' => 587
        );
    }
}
