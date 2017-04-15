<?php

namespace Wastetopia\Controller;
use Wastetopia\Model\MessageModel;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Wastetopia\Config\CurrentConfig;
use Wastetopia\Model\CardDetailsModel;
use Wastetopia\Model\ConversationListModel;

class MessageController
{
	
	function __construct()
	{

		// Card details model
		$this->cardDetailsModel = new CardDetailsModel();
		
	    //Create MessageModel instance
        $this->model = new MessageModel();

        //Load Twig environment
        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);

	}


    function generatePageFromListing($listingID){
	$conversationIDs = $this->model->getConversationIDFromListing($listingID);
	if (count($conversationIDs) > 0){
	    // Conversation already exists	
	    $conversationID = $conversationIDs[0];
	}else{
	   // Create the conversation 	
	   $conversationModel = new ConversationListModel();
	   $conversationModel->createConversation($listingID);
	   $conversationIDs = $this->model->getConversationIDFromListing($listingID);
	   $conversationID = $conversationIDs[0];	
	}
	$conversationID = $conversationID["ConversationID"];  
	return $this->generatePage($conversationID);    
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
	print_r($details);    
	$details = $details[0];
        $userName = $details["Forename"]." ".$details["Surname"];
        $userID = $details["UserID"]; //ID of other user in conversation
        $senderImage = $this->cardDetailsModel->getUserImage($userID); //Profile picture of other user
        $senderName = $userName;//." - ".$itemName;
	

	$CurrentConfig = new CurrentConfig();  
	$config = $CurrentConfig->getAll();    
        $output = array(
            "config" => $config,
            "senderName"=>$senderName,
            "senderImage"=>$senderImage,
            "conversationID" =>$conversationID,  //Needed so page can poll for new messages with the ID
            "messages"=>$messageDisplayHTML,
            "listingPanel"=>$listingPanel);

        //Load template and print result
        $template = $this->twig->loadTemplate('/messaging/MessagePage.twig');
        return $template->render($output);
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
			$messageContent = $row['Content'];
			$messageSenderID = $row['UserID'];
			$messageSenderName = $row['Forename']." ".$row['Surname'];
			$messageTimeStamp = $row["Time"];

			$message = array();
			$message['content'] = $messageContent;
			

			$message['sender'] = ($messageSenderID == $currentUser); //1 if current user sent the message
            		$message['timeStamp'] = $messageTimeStamp;
			
			
			array_push($messages, $message);
		}

		$output = array("messages" => $messages);


		//MessageDisplay.twig
		$template = $this->twig->loadTemplate('/messaging/MessageDisplay.twig');

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

        $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);
        $itemName = $listing["ItemName"];
        $expiryDate = $listing["Use_By"];
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
		
		$giverOrReceiver = $this->model->checkIfReceiver($conversationID);
		
		$result = $this->model->sendMessage($conversationID, $message, $giverOrReceiver);
		
		//For option 2 in messages.js
        //$html = $this->generatePage($conversationID);
		
		return $result;
	}
	
	
	
}


?>
