<?php

use Klein\Klein;
use Wastetopia\Controller\Login_Controller;

require_once '../vendor/autoload.php';

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

$klein->respond("GET", "/login", function() {
    return Login_Controller::index();
});

$klein->respond("GET", "/register", function() {
   return "Registering page";
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

$klein->dispatch();


