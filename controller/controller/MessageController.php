<?php

include '../model/model/MessageModel.php';

class MessageController
{
	
	function __construct()
	{

	    //Create MessageModel instance
        $this->model = new MessageModel();

        //Load Twig environment
        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);

	}


    /**
     * Generates (and prints) HTML for messaging page with initial conversation loaded
     * @param $conversationID
     */
    function generatePage($conversationID)
    {
        //HTML For message box display
        $messageDisplayHTML = $this->generateMessageDisplay($conversationID);

        //Array of details for listing view
        $listingPanel = $this->generateItemViewPanel($conversationID);

        //Get details of conversation (names)
        $details = $this->model->getConversationDetails($conversationID);
        $userName = $details["Forename"]." ".$details["Surname"];
        //$itemName = $details["name"];
        $userID = $details["UserID"]; //ID of other user in conversation
        $senderImage = $this->model->getUserImage($userID); //Profile picture of other user
        $senderName = $userName;//." - ".$itemName;


        $output = array("senderName"=>$senderName,
            "senderImage"=>$senderImage,
            "conversationID" =>$conversationID,  //Needed so page can poll for new messages with the ID
            "messages"=>$messageDisplayHTML,
            "listingPanel"=>$listingPanel);

        //Load template and print result
        $template = $this->twig->loadTemplate('MessagePag.twig');
        print($template->render($output));
    }


    /**
     * Generates the HTML for the messages in the message box (Use this function to update the messages on the page)
     * @param $conversationID
     * @return mixed (HTML)
     */
    function generateMessageDisplay($conversationID)
	{

	    $currentUser = $this->model->getUserID();

		// Set them as read
		$confirm = $this->model->setMessagesAsRead($conversationID);

        // Get the messages
        $messageResults = $this->model->getMessagesFromConversation($conversationID);

        //Do all the processing of variables here
		$messages = array();
		
		foreach($messageResults as $row)
		{
			$messageContent = $row['content'];
			$messageSenderID = $row['UserID'];
			$messageSenderName = $row['Forename']." ".$row['Surname'];
			
			$message = array();
			$message['content'] = $messageContent;
			$message['sender'] = ($messageSenderID == $currentUser); //1 if user sent the message
            $message['timeStamp'] = $messageContent["time"]; //Time stamp
			
			array_push($messages, $message);
		}

		$output = array("messages" => $messages);

		//MessageDisplay.twig
		$template = $this->twig->loadTemplate('MessageDisplay.twig');

		//print_r(json_encode($output));
		return $template->render($output);
		
	}


    /**
     * Generates the output array for listing view side panel (for use in twig file)
     * @param $conversationID
     * @return mixed
     */
    function generateItemViewPanel($conversationID)
    {
        $generalDetails = $this->model->getListingDetails($conversationID);
        $listing = $generalDetails[0]; //ListingID, ItemName, Use_By_Date, LocationName, Post_Code
        $listingID = $listing["ListingID"];
        $defaultImage = $this->model->getDefaultImage($listingID);
        $itemName = $listing["ItemName"];
        $expiryDate = $listing["Use_By_Date"];
        $locationName = $listing["LocationName"];
        $postCode = $listing["Post_Code"];

        //Generate array of details
        $output = array("defaultImage" => $defaultImage,
                        "itemName" => $itemName,
                        "expiryDate"=> $expiryDate,
                        "location" => $locationName.", ".$postCode);

        return $output;
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
