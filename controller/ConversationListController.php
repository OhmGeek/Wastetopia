<?php

namespace Wastetopia\Controller;
use Wastetopia\Model\ConversationListModel;
use Twig_Loader_Filesystem;
use Twig_Environment;

class ConversationListController
{
	
	function __construct()
	{
	    //Create UserModel instance
        $this->model = new UserModel();

	    //Create twig loader
        $loader = new Twig_Loader_Filesystem('../view/twig_version/');
        $this->twig = new Twig_Environment($loader);

	}
	

    /**
     * Generates HTML for list of people/conversations
     * @return mixed
     */
    function generatePage()
	{
		
		$receivingTabHTML = $this->generateReceivingTabHTML();
		$sendingTabHTML = $this->generateSendingTabHTML();

		//Create array for Twig file
		$output = array(
				"receivingList" => $receivingTabHTML,
				"givingList" => $sendingTabHTML
			);

		//Load template and print result
		$template = $this->twig->loadTemplate('MessagesListPage.twig');
		print($template->render($output));
	}


    /**
     * Generates HTML for receiving tab
     * @return mixed
     */
    function generateReceivingTabHTML()
    {
        $receivingResults = $this->model->getConversationsReceiving();
		
	//Create arrays of conversation details from results
	$receiving = $this->createConversationArray($receivingResults);

	$template = $this->twig->loadTemplate('MessagesTabsDisplay.twig');
	
	return $template->render(array("conversationList"=>$receiving, "giving" => 0));
		
    }


    /**
     * Generates HTML for sending tab
     * @return mixed
     */
    function generateSendingTabHTML()
    {
	$sendingResults = $this->model->getConversationsSending();
		
	//Create arrays of conversation details from results
	$sending = $this->createConversationArray($sendingResults);


	$template = $this->twig->loadTemplate('MessagesTabsDisplay.twig');
		
	return $template->render(array("conversationList"=>$sending, "giving" => 1));
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

            $conversation = array();
	    $conversation["userImage"] = $userImage;
            $conversation['conversationID'] = $conversationID;
            $conversation['userName'] = $firstName." ".$lastName;
            $conversation['item'] = $itemName;
            $conversation['notifications'] = $unread;

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

		$this->model->createConversation(listingID);
		
		return;
	}


    /**
     * Deletes a given conversation and it's associated messages
     * @param $conversationID
     */
    function deleteConversation($conversationID)
    {

	    $this->model->deleteConversation($conversationID);
    }
	
}


?>