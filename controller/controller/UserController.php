<?php

include '../model/UserModel.php';

class UserController
{
	
	function __construct()
	{
	    //Create UserModel instance
        $this->model = new UserModel();

	    //Create twig loader
        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);

        //Create HTML page
		print($this->generatePage());
	}
	

    /**
     * Generates HTML for list of people/conversations
     * @return mixed
     */
    function generatePage()
	{
		$receivingResults = $this->model->getConversationsReceiving();
		$sendingResults = $this->model->getConversationsSending();
		
		//Create arrays of conversation details from results
		$receiving = $this->createConversationArray($receivingResults);
		
		$sending = $this->createConversationArray($sendingResults);

		//Create array for Twig file
		$output = array(
				"receiving" => $receiving,
				"sending" => $sending
			);

		//Load template and print result
		$template = $this->twig->loadTemplate('user.twig');
		return $template->render($output);
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
            $firstName = $row['Forename'];
            $lastName = $row['Surname'];
            $conversationID = $row['conversation_id'];
            $itemName = $row['Name'];
            $unread = $row['count']; //Not sure whether to send back the actual number or a boolean or css style attribute

            $conversation = array();
            $conversation['otherUserId'] = $otherUser;
            $conversation['conversationID'] = $conversationID;
            $conversation['name'] = $firstName." ".$lastName;
            $conversation['itemName'] = $itemName;
            $conversation['notification'] = $unread;//????? Undecided

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