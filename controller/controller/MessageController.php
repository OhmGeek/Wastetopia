<?php

include '../model/MessageModel.php';

class MessageController
{
	
	function __construct($conversationID)
	{
        //Load Twig environment
        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);

        //Create HTML page
		print($this->generatePage($conversationID));
	}

	
	// Sends back messages in a given converstion
	function generatePage($conversationID)
	{
		$Model = new MessageModel();
		
		// Get the messages
		$messageResults = $Model->getMessagesFromConversation($conversationID);
	
		// Set them as read
		$confirm = $Model->setMessagesAsRead($conversationID);
		
		
		//Do all the processing of variables here
		$messages = array();
		
		foreach($messageResults as $row)
		{
			$messageContent = $row['content'];
			$messageSenderID = $row['UserID'];
			$messageSenderName = $row['Forename']." ".$row['Surname'];
			
			$message = array();
			$message['content'] = $messageContent;
			$message['fromID'] = $messageSenderID;
			$message['fromName'] = $messageSenderName;
			
			array_push($messages, $message);
		}
		
		$output = array("messages" => $messages);
	
		$loader = new Twig_Loader_Filesystem('../view/');
		$twig = new Twig_Environment($loader);
		
		$template = $twig->loadTemplate('message.twig');

		//print_r(json_encode($output));
		return $template->render($output);
		
	}


	// Sends a message in the conversation
	function sendMessage($conversationID, $message)
	{
		$Model = new MessageModel();
		
		$giverOrReceiver = checkIfReceiver($conversationID);
		
		$result = $Model->sendMessage($conversationID, $message, $giverOrReceiver);

		//For option 2 in messages.js
        //$html = $this->generatePage($conversationID);
		
		return $result;
	}
	
	
	
}


?>