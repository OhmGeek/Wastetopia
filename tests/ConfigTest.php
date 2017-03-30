<?php

/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 28/02/17
 * Time: 15:50
 */
require_once(__DIR__ . '/../vendor/autoload.php');


use Wastetopia\Config\LocalConfig;

class ConfigTest extends PHPUnit_Framework_TestCase
{
//    /** @test */
//    public function testProduction()
//    {
//        $config = (new ProductionConfig())->getConfiguration();
//
//        $this->assertEquals($config['DB_USER'],"dcs8s04");
//        $this->assertEquals($config['DB_HOST'],"mysql.dur.ac.uk");
//        $this->assertEquals($config['DB_USER'],"dcs8s04");
//    }

    /** @test */
    public function testLocal()
    {
        $config = (new LocalConfig())->getConfiguration();

        $this->assertEquals($config['DB_HOST'],"localhost");
        $this->assertEquals($config['DB_USER'],"root");
    }
}
