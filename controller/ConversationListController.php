<?php

namespace Wastetopia\Controller;
use Wastetopia\Model\ConversationListModel;
use Wastetopia\Model\HeaderInfo;
use Wastetopia\Model\MessageModel;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Wastetopia\Config\CurrentConfig;

/**
 * Class ConversationListController - Used to generate and handle inputs on the ConversationListPage
 * @package Wastetopia\Controller
 */
class ConversationListController
{

    /**
     * ConversationListController constructor.
     */
    function __construct()
	{		
		
	    //Create ConversationModel instance
        $this->model = new ConversationListModel();
	    
	    $this->MessageModel = new MessageModel();

        // Create MessageModel instance
	$this->messageModel = new MessageModel();
		
	    //Create twig loader
        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);

	}
	

    /**
     * Generates HTML for list of people/conversations
     * @return mixed
     */
    function generatePage()
	{

	    // Generate HTML for tabs
		$receivingTabHTML = $this->generateReceivingTabHTML();
		$sendingTabHTML = $this->generateSendingTabHTML();

	    
	    // Get config
		$CurrentConfig = new CurrentConfig();
	        $CurrentConfig->loadConfig("production");
		$config = $CurrentConfig->getAll();
	    
		//Create array for Twig file
		$output = array(
		        "config" => $config,
				"receivingList" => $receivingTabHTML,
				"givingList" => $sendingTabHTML,
                "header" => HeaderInfo::get()
			);

		//Load template and print result
		$template = $this->twig->loadTemplate('/messaging/MessagesListPage.twig');
		return $template->render($output);
	}


    /**
     * Generates HTML for receiving tab
     * @return mixed
     */
    function generateReceivingTabHTML()
    {
        // Get all conversations for items user is requesting
        $receivingResults = $this->model->getConversationsReceiving();
		
        //Create arrays of conversation details from results
        $receiving = $this->createConversationArray($receivingResults);
	$isEmpty = (count($receiving) == 0);
	    
	    
	// Get config
		$CurrentConfig = new CurrentConfig();
	        $CurrentConfig->loadConfig("production");
		$config = $CurrentConfig->getAll();
	    
        $template = $this->twig->loadTemplate('messaging/MessagesTabsDisplay.twig');

        return $template->render(array("config" => $config,"isEmpty" => $isEmpty, "conversationList"=>$receiving, "giving" => 0));
		
    }


    /**
     * Generates HTML for sending tab
     * @return mixed
     */
    function generateSendingTabHTML()
    {
        // Get all conversations for items user is offering
        $sendingResults = $this->model->getConversationsSending();

        //Create arrays of conversation details from results
        $sending = $this->createConversationArray($sendingResults);
	    $isEmpty = (count($sending) == 0);
	    
	    
	  // Get config
		$CurrentConfig = new CurrentConfig();
	        $CurrentConfig->loadConfig("production");
		$config = $CurrentConfig->getAll();
	    
        $template = $this->twig->loadTemplate('messaging/MessagesTabsDisplay.twig');

        return $template->render(array("config" => $config,"isEmpty" => $isEmpty, "conversationList"=>$sending, "giving" => 1));
    }


    /**
     * Constructs an array with the correct variables, given an array returned by a function from UserModel
     * @param $conversations
     * @return array
     */
    function createConversationArray($conversations)
    {
        $results = array();
        foreach($conversations as $row)
        {
            $otherUser = $row['UserID'];
	        $userImage = $this->model->getUserImage($otherUser);
            $firstName = $row['Forename'];
            $lastName = $row['Surname'];
            $conversationID = $row['ConversationID'];
            $itemName = $row['Name'];
            $unread = $row['count']; 
            $listingID = $row["ListingID"]; // Used instead of conversationID

            $conversation = array();
	        $conversation["userImage"] = $userImage;
            $conversation['conversationID'] = $conversationID;
            $conversation['userName'] = $firstName." ".$lastName;
            $conversation['item'] = $itemName;
            $conversation['numUnread'] = $unread;
            $conversation["listingID"] = $listingID;		

            array_push($results, $conversation);
        }
        return $results;
    }


    /**
     * Creates a new conversation between users for a given Listing
     * @param $listingID
     */
    function createNewConversation($listingID)
	{

		$this->model->createConversation($listingID);
		
		return;
	}


    /**
     * Deletes a conversation from the given listingID and it's associated messages
     * @param $listingID
     */
    function deleteConversation($listingID)
    {
	    error_log("Deleting: ".$listingID);
	    
	    $conversationIDs = $this->MessageModel->getConversationIDFromListing($listingID);
	    $conversationID = $conversationIDs[0];
	    $conversaitionID = $conversationID["ConversationID"];
	    
	    error_log("Conversation: ".$conversationID);
	    $this->model->deleteConversation($conversationID);
    }
	
}


?>
