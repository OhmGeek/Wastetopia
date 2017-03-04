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
		//print($this->generatePage($conversationID));
	}


    /**
     * Generates (and prints) HTML for messaging page with initial conversation loaded
     * @param $conversationID
     */
    function generatePage($conversationID)
    {
        //HTML For message box display
        $messageList = $this->generateMessageDisplay($conversationID);

        //HTML for listing view
        $listingPanel = $this->generateItemViewPanel($conversationID);

        //Get details of conversation (names)
        $details = $this->model->getConversationDetails($conversationID);
        $userName = $details["Forename"]." ".$details["Surname"];
        $itemName = $details["name"];
        $conversationName = $userName." - ".$itemName;



        $output = array("conversationName"=>$conversationName,
            "conversationID" =>$conversationID,  //Needed so page can poll for new messages with the ID
            "message-list"=>$messageList,
            "listingPanel"=>$listingPanel);

        //Load template and print result
        $template = $this->twig->loadTemplate('MAIN TWIG VIEW HERE');
        print($template->render($output));
    }


    /**
     * Generates the HTML for the message box part of the page (Use this function to update the messages on the page)
     * @param $conversationID
     * @return mixed (HTML)
     */
    function generateMessageDisplay($conversationID)
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

		
		$template = $this->twig->loadTemplate('MESSAGE_BOX_TWIG');

		//print_r(json_encode($output));
		return $template->render($output);
		
	}


    /**
     * Generates HTML for listing view side panel on messaging page
     * @param $conversationID
     * @return mixed
     */
    function generateItemViewPanel($conversationID)
    {
        $generalDetails = $this->model->getListingDetails($conversationID);
        $listing = $generalDetails[0]; //ListingID, ItemName, Use_By_Date, LocationName, Post_Code
        $listingID = $listing["ListingID"];
        $defaultImage = $this->model->getDefaultImage($listingID);

        //Generate array of details
        $output = array();



        $template = $this->twig->loadTemplate('PANEL_TWIG');

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