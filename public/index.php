<?php

use Klein\Klein;
use Wastetopia\Controller\ConversationListController;
use Wastetopia\Controller\Login_Controller;
use Wastetopia\Config\CurrentConfig;
use Wastetopia\Controller\MessageController;

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

// todo authenticate on messages. Must be logged in to view correct messages.
$klein->with('/messages', function () use ($klein) {

    $klein->respond('GET', '/?', function ($request, $response) {
        // Show all conversations
        $controller = new ConversationListController();
        return $controller->generatePage();
    });

    $klein->respond('GET', '/[:conversationID]', function ($request, $response) {
        // view a specific conversation
        $conversationID = $request->conversationID;
        $controller = new MessageController();
        return $controller->generatePage($conversationID);
    });

    // these are the API based messaging tasks
    // todo: error/failure in response.
    $klein->respond('POST', '/send', function ($request, $response) {
        //send a message
        //we need the conversationID and the message.
        $conversationID = $request->conversationID;
        $message = $request->message;

        $controller = new MessageController();
        $controller->sendMessage($conversationID,$message);
        return "";
    });

    $klein->respond('POST', '/delete-conversation', function ($request, $response) {
        $controller = new ConversationListController();
        $conversationID = $request->conversationID;
        $controller->deleteConversation($conversationID);
        return "";
    });

    $klein->respond('GET', '/poll-messages', function ($request, $response) {
        $conversationID = $request->conversationID;
        $controller = new MessageController();
        return $controller->generateMessageDisplay($conversationID);
    });

    $klein->respond('GET', '/poll-giving', function ($request, $response) {
        $controller = new ConversationListController();
        return $controller->generateGivingTabHTML;
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


