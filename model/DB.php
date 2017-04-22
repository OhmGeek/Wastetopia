<?php

namespace Wastetopia\Model;

use PDO;
use Wastetopia\Config\CurrentConfig;

class DB {

    /**
     * Get a new database object
     * @return PDO (PDO object)
     */
    public static function getDB()
	{
	    $host = CurrentConfig::getProperty('DB_HOST');
	    $name = CurrentConfig::getProperty('DB_NAME');
	    $user = CurrentConfig::getProperty('DB_USER');
	    $pass = CurrentConfig::getProperty('DB_PASS');

        return new PDO('mysql:host=' . $host . ';dbname=' . $name,$user,$pass);
    }

}


