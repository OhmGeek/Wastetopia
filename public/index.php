<?php

require_once '../vendor/autoload.php';

use Klein\Klein;
use Wastetopia\Controller\Login_Controller;
use Wastetopia\Config\CurrentConfig;
use Wastetopia\Controller\LoginController;
use Wastetopia\Controller\SearchController;



// check if we should use production? Otherwise, use community.
$mode = $_ENV['MODE'];
$config = new CurrentConfig();
$config->loadConfig($mode);

$base  = dirname($_SERVER['PHP_SELF']);

// Update request when we have a subdirectory
if(ltrim($base, '/')){
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($base));
}

// Dispatch as always
$klein = new Klein();

$klein->respond("GET", "/", function() {
    return "HomePage";
});


$klein->with('/search', function () use ($klein) {

    $klein->respond('GET', '/json/[:search]', function ($request, $response) {
        $search = new SearchController();
        return $search->basicSearch($request->search);
    });
    $klein->respond('GET', '/json/sample', function ($request, $response) {
        $search = new SearchController();
        return $search->sampleSearch();
    });
    $klein->respond('GET', '/json/[:lat]/[:long]/[:search]/[:tags]', function ($request, $response) {
        $search = new SearchController();
        return $search->distanceSearch($request->lat, $request->long, $request->search, $request->tags);
    });
});

$klein->respond("GET", "/login", function($request, $response) {
    $controller = new LoginController();
    return $controller->index($response);
});

$klein->respond("GET", "/register", function() {
   return "Registering page";
});

$klein->respond("GET", "/get-env", function() {
   $envStr = "DB Host: " . $_ENV['DB_HOST'] . "\n";
    $envStr .= "DB_NAME " . $_ENV['DB_NAME'] . "\n";
    $envStr .= "DB_USER " . $_ENV['DB_USER'] . "\n";
    $envStr .= "DB_PASS " . $_ENV['DB_PASS'] . "\n";
    echo "Printing stuff now \n";
    return $envStr;
});

$klein->with('/items', function () use ($klein) {
    $klein->respond('GET', '/?', function ($request, $response) {
        // Generic Items Page
        return "Main Item Page";
    });

    $klein->respond('GET', '/[:id]', function ($request, $response) {
        // Show a single user
        $itemID = $request->id;
        return "Show Item " . $itemID;
    });

});

$klein->with('/api', function () use ($klein) {
    $klein->respond('POST', '/verify-login', function ($request, $response) {
        $controller = new LoginController();
        $username = $request->email;
        $password = $request->password;
        $dest = $_ENV['ROOT_BASE'];
        return $controller->login($username, $password, $dest, $response);
    });

    $klein->respond('GET', '/[:id]', function ($request, $response) {
        // Show a single user
        $itemID = $request->id;
        return "Show Item " . $itemID;
    });

});


$klein->onHttpError(function ($code, $router) {
    switch ($code) {
        case 404:
            $router->response()->body(
                'Y U so lost?!'
            );
            break;
        case 405:
            $router->response()->body(
                'You can\'t do that!'
            );
            break;
        default:
            $router->response()->body(
                'Oh no, a bad error happened that caused a '. $code
            );
    }
});


$klein->dispatch();


