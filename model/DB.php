<?php

use PDO;

class DB {
    private static function initDefaultDB() {
        $_ENV['DB_HOST'] = 'mysql.dur.ac.uk';
		$_ENV['DB_NAME'] = 'Idcs8s04_Wastetopia';
		$_ENV['DB_USER'] = 'dcs8s04';
		$_ENV['DB_PASS'] = 'when58';
	}
    public static function getDB()
	{
		if(!isset($_ENV['DB_HOST']) || !isset($_ENV['DB_NAME']) || !isset($_ENV['DB_USER']) || !isset($_ENV['DB_PASS'])) {
			DB::initDefaultDB();
		}


        return new PDO("mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'],$_ENV['DB_USER'],$_ENV['DB_PASS']);
    }

}


