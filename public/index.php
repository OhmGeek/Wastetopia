<?php

use Klein\Klein;
use Wastetopia\Controller\Login_Controller;
use Wastetopia\Config\CurrentConfig;
require_once '../vendor/autoload.php';

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

$klein->with('/test', function () use ($klein) {

    $klein->respond('GET', '/?', function ($request, $response) {
        // File upload test
        $loader  = new Twig_Loader_Filesystem(__DIR__.'/../view/');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('image_upload_example.twig');
        return $template->render(array());
    });

    $klein->respond('POST', '/upload_files', function ($request, $response) {
        // Show a single user
        $m = new \Wastetopia\Model\AmazonS3();
        $m->upload($request->files());
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


