<?php
use PDO;
namespace Wastetopia\Model;


use Wastetopia\Config\CurrentConfig;

class DB {
    private static function initDefaultDB() {

	}
    public static function getDB()
	{
	    $host = CurrentConfig::getProperty('DB_HOST');
	    $name = CurrentConfig::getProperty('DB_NAME');
	    $user = CurrentConfig::getProperty('DB_USER');
	    $pass = CurrentConfig::getProperty('DB_PASS');

        return new PDO("mysql:host=" . $host . ";dbname=" . $name,$user,$pass);
    }

}


