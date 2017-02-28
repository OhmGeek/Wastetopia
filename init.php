<?php
/**
 * This sets up a server
 * Created by PhpStorm.
 * User: ryan
 * Date: 28/02/17
 * Time: 13:46
 */

function loadConfigFromArray($details) {
    foreach ($details as $key => $val) {
        $_ENV[$key] = $val;
    }
}
$mode = $argv[0];

// default to using prod.
if(!isset($mode)) {
    $mode = 'prod';
}

$config = null;

// select the modes
switch ($mode) {
    case 'prod':
        $config = (new ProductionConfig())->getConfiguration();
        break;
    case 'local':
        $config = (new LocalConfig())->getConfiguration();
        break;
    default:
        die("No config specified");
        break;
}

loadConfigFromArray($config);


