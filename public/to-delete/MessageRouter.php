<?php
/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 04/03/2017
 * Time: 18:40
 */

include '../vendor/autoload.php';
use Wastetopia\Controller\MessageController;
use Wastetopia\Controller\ConversationListController;

$routeType = $_GET["type"];

if ($routeType == "view"){
    //View the message page
    $conversationID = $_GET["conversationID"];
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
   echo("SENDING MESSAGE");
    $conversationID = $_GET["conversationID"];
    $message = $_GET["message"];
    $controller = new MessageController();
    $controller->sendMessage($conversationID, $message);
}elseif($routeType == "conversationListing"){
    $controller = new ConversationListController();
    $controller->generatePage();
}elseif($routeType == "pollGiving"){
    $controller = new ConversationListController();
    echo ($controller->generateGivingTabHTML);
}elseif($routeType == "pollReceiving"){
    $controller = new ConversationListController();
    echo ($controller->generateReceivingTabHTML);

}elseif($routeType == "deleteConversation"){
    $controller = new ConversationListController();
    $conversationID = $_GET["ConversationID"];
    $controller->deleteConversation($conversationID);
}


?>
