<?php

require_once '../vendor/autoload.php';
use Klein\Klein;
use Wastetopia\Controller\AddItemController;
use Wastetopia\Controller\Login_Controller;
use Wastetopia\Controller\ViewItemController;
use Wastetopia\Config\CurrentConfig;



// check if we should use production? Otherwise, use community.
$mode = $_ENV['MODE'];
$config = new CurrentConfig();
$config->loadConfig($mode);

$base  = dirname($_SERVER['PHP_SELF']);
//
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

$klein->respond("GET", "/get-env", function() {
   $envStr = "DB Host: " . $_ENV['DB_HOST'] . "\n";
    $envStr .= "DB_NAME " . $_ENV['DB_NAME'] . "\n";
    $envStr .= "DB_USER " . $_ENV['DB_USER'] . "\n";
    $envStr .= "DB_PASS " . $_ENV['DB_PASS'] . "\n";
    echo "Printing stuff now \n";
    return $envStr;
});

$klein->with('/items', function () use ($klein) {

    $klein->respond('GET', '/add', function($request, $response) {
        $control = new AddItemController();
        return $control->renderAddPage();
    });


    $klein->respond('GET', '/?', function ($request, $response) {
        // Generic Items Page
        return "Main Item Page";
    });


    $klein->respond('GET', '/[:id]', function ($request, $response) {
        // Show a single user
        $itemID = $request->id;
        $controller = new ViewItemController();
        return $controller->getListingPage($itemID);

    });

});

// NOW DEAL WITH API STUFF
// This just returns JSON versions of the views, useful for javascript/testing.
$klein->respond('POST', '/api/items/add', function ($request, $response, $service, $app) {
    // todo validate each field server side (and return false if not with an error message
    // Take in a JSON of things needed to add items
    // make a post request to add this item, and return whether it was successful or not (TODO return success from DB).
    $details = json_decode($request->details, true);
    $control = new AddItemController();
    $control->addItem($details);
});

$klein->respond('GET', '/api/items/view/[:id]', function($request, $response) {
    $itemID = $request->id;
    $controller = new ViewItemController();
    return $controller->getListingDetailsAsJSON($itemID);
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


