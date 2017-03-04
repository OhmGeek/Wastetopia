<?php
/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 04/03/2017
 * Time: 18:40
 */

include '../controller/controller/MessageController.php';

$routeType = $_GET["type"];

//Polling for messages
//Return HTML for messages in conversation
if ($routeType == "poll"){
    $conversationID = $_GET["conversationID"];
    $controller = new MessageController();
    echo($controller->generateMessageDisplay($conversationID));
}elseif($routeType == "send"){
    $conversationID = $_GET["conversationID"];
    $message = $_GET["message"];
    $controller = new MessageController();
    $controller->sendMessage($conversationID, $message);
}


?>
