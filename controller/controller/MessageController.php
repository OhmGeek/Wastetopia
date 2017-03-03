<?php

include '../model/MessageModel.php';

class MessageController
{
	
	function __construct($conversationID)
	{

	    //Create MessageModel instance
        $this->model = new MessageModel();

        //Load Twig environment
        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);


        //Create HTML page
		print($this->generatePage($conversationID));
	}

	

    /**
     * Generates the HTML for the message display part of the page
     * @param $conversationID
     * @return mixed (HTML)
     */
    function generatePage($conversationID)
	{
		
		// Get the messages
		$messageResults = $this->model->getMessagesFromConversation($conversationID);
	
		// Set them as read
		$confirm = $this->model->setMessagesAsRead($conversationID);
		
		
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


    /**
     * Sends a message in the conversation
     * @param $conversationID
     * @param $message
     * @return mixed (Either HTML (option 2 in messages.js) or a boolean (option1 in messages.js))
     */
    function sendMessage($conversationID, $message)
	{

		$giverOrReceiver = checkIfReceiver($conversationID);
		
		$result = $this->model->sendMessage($conversationID, $message, $giverOrReceiver);

		//For option 2 in messages.js
        //$html = $this->generatePage($conversationID);
		
		return $result;
	}
	
	
	
}


?>