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
	
	
	// Used when user is selected
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


    //Used to extract data from Model results
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


    //Used to create a new conversation
	function createNewConversation($otherUser, $itemName)
	{

		$this->model->createConversation($otherUser, $itemName);
		
		return;
	}

	function deleteConversation($conversationID)
    {

	    $this->model->deleteConversation($conversationID);
    }
	
}


?>