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

    private static $configMode = "production";

    public function __construct()
    {
        if(self::$configMode === "production") {
            self::$currentConfig = (new ProductionConfig())->getConfiguration();
        }
        elseif(self::$configMode === "local") {
            self::$currentConfig = (new LocalConfig())->getConfiguration();
        }
    }

    public function loadConfig($mode) {
        $mode = "production";
        if($mode === "production") {
            self::$currentConfig = (new ProductionConfig())->getConfiguration();
        }
        elseif($mode === "local") {
            self::$currentConfig = (new LocalConfig())->getConfiguration();
        }
    }

    public static function getProperty($prop) {
        return self::$currentConfig[$prop];
    }

    public static function getAll() {
        return self::$currentConfig;
    }
}