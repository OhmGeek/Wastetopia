<?php
/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 04/03/2017
 * Time: 18:40
 */

include '../control/MessageController.php';

$routeType = $_GET["type"];


if ($routeType == "view"){
    //View the message page
    $controller = new MessageController();
    echo($controller->generatePage($conversationID));
}
elseif($routeType == "poll"){
    //Polling for messages
    //Return HTML for messages in conversation
    $conversationID = $_GET["conversationID"];
    $controller = new MessageController();
    echo($controller->generateMessageDisplay($conversationID));
}elseif($routeType == "send"){
    //Send a message
    $conversationID = $_GET["conversationID"];
    $message = $_GET["message"];
    $controller = new MessageController();
    $controller->sendMessage($conversationID, $message);
}


?>