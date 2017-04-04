<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 25/03/17
 * Time: 16:11
 */

namespace Wastetopia\Config;


class CurrentConfig
{
    private static $currentConfig;

    public function __construct()
    {
        // load the local config as default
        $currentConfig = (new LocalConfig())->getConfiguration();
    }

    public function loadConfig($mode) {
        if($mode === "production") {
            $currentConfig = (new ProductionConfig())->getConfiguration();
        }
        // otherwise, we don't do anything.
    }

    public static function getProperty($prop) {
        if(!self::$currentConfig) {
            self::$currentConfig = (new ProductionConfig())->getConfiguration();
        }
        return self::$currentConfig[$prop];
    }

    public static function getAll() {
        if(!self::$currentConfig) {
            self::$currentConfig = (new ProductionConfig())->getConfiguration();
        }
        return self::$currentConfig;
    }
}