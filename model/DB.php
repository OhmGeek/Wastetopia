<?php

use PDO;

class DB {
    private static function initDefaultDB() {

	}
    public static function getDB()
	{
		if(!isset($_ENV['DB_HOST']) || !isset($_ENV['DB_NAME']) || !isset($_ENV['DB_USER']) || !isset($_ENV['DB_PASS'])) {
			die("Error, no database details specified. Please run the init script.");
		}

        return new PDO("mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'],$_ENV['DB_USER'],$_ENV['DB_PASS']);
    }

}


