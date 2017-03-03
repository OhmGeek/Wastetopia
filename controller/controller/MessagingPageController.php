<?php

include '../model/UserController.php'
include '../model/MessageController.php'

class MessagingPageController
{
	
	function __construct($conversationID = NULL)
	{
        //Load Twig environment
        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);

        //Generate HTML for page
	    if ($conversationID === NULL){
	        $this->generateNormalPage();
        }else {
            $this->generateWorkingPage($listingID);
        }
	}
	
	//Generate page with conversations in sidebar and empty message area
	function generateNormalPage()
	{
		//Return html of people list 
		$peopleList = new UserController();
		
		$output = array("people-list"=>$peopleList,
						"conversationName"=>"Conversations",
						"message-list"=>NULL);
		//Create twig loader
        //Create template
        //Display output
		
	}
	
	//Generate page with conversation sidebar and a conversation loaded in the message area
	function generateWorkingPage($conversationID)
	{
		//Return Twig of page with people list and conversation
		$Model = new UserModel();
		$conversationID;// = $Model->getConversationFromListing($listingID);
		
		//Conversation doesn't exist
		if ($conversationID === False){
			$Model->createConversation($listingID);
			$conversationID = getConversationFromListing($listingID);
		}
		
		//$senderName = $UserController->getSenderByConversationID($conversationID); <- Implement this method in UserController
		
		//HTML For message display
		$messageList = new MessageController($conversationID);
		
		//Return html of people list 
		$peopleList = new UserController();
		
	
		$output = array("people-list"=>$peopleList,
						"conversationName"=>$senderName,
						"message-list"=>$messageList);
	}
}

?>