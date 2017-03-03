<?php

class MessageModel
{
	function __construct()
	{
		$this->db = DB::getDB():
	}

    /**
     * Returns the ID of the user currently logged in
     * @return string
     */
    private function getUserID()
    {
        $reader = new UserCookieReader();
        return $reader->get_user_id();
    }



    /**
     * Return all messages in the conversation betweeen you and another user
     * @param $conversationID
     * @return mixed
     */
    function getMessagesFromConversation($conversationID)
	{

		$statement = $this->db->prepare("SELECT Message.Content, Message.Time User.UserID, User.Forename, User.Surname
								FROM Message, User, Conversation, Listing
								WHERE Message.FK_Conversation_ConversationID = :conversationID
								AND Conversation.ConversationID = :conversationID2
								AND ((Message.Giver_Or_Receiver = 1 
										AND User.UserID = Conversation.FK_User_ReceiverID)
									OR (Message.Giver_Or_Receiver = 0
										AND Conversation.FK_Listing_ListingID = Listing.ListingID
										AND Listing.FK_User_UserID = User.UserID))
								GROUP BY Message.MessageID
								ORDER BY time ASC;");
								
		$statement->bindValue(':conversationID',$conversationID, PDO::PARAM_INT);							
		$statement->bindValue(':conversationID2',$conversationID, PDO::PARAM_INT);							

		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}



    /**
     * Sets all messages in a conversation sent by $otherUser to read`
     * @param $conversationID
     * @return bool (True if successful)
     */
    function setMessagesAsRead($conversationID)
    {

		$currentUser = $this->getUserID();

		$statement = $this->db->prepare("UPDATE Message, Listing, Conversation
								SET Message.`Read` = 1
								WHERE Message.FK_Conversation_ConversationID = :conversationID
								AND Message.FK_Conversation_ConversationID = Conversation.ConversationID
								AND Message.`Read` = 0
								AND ((Giver_Or_Receiver = 1 AND NOT(Conversation.FK_User_ReceiverID=:currentUser))  
								OR (Giver_Or_Receiver = 0 AND Conversation.FK_User_ReceiverID=:currentUser2))");
		
		$statement->bindValue(':conversationID', $conversationID, PDO::PARAM_INT);
		$statement->bindValue(':currentUser', $currentUser, PDO::PARAM_INT);
		$statement->bindValue(':currentUser2', $currentUser, PDO::PARAM_INT);
		
		$statement->execute();
		
		return true;
	}


    /**
     * Sends message to specified user
     * @param $conversationID
     * @param $message
     * @param $giverOrReceiver
     * @return mixed
     */
    function sendMessage($conversationID, $message, $giverOrReceiver)
    {

        $currentUser = $this->getUserID();
		 
		$statement = $this->db->prepare("INSERT INTO Message (FK_Conversation_ConversationID, Content, Giver_Or_Receiver)
									VALUES (:conversationID, :content, :giverOrReceiver);");
		 
		$statement->bindValue(":conversationID", $conversationID, PDO::PARAM_INT);
		$statement->bindValue(":content", $message, PDO::PARAM_STR);
		$statement->bindValue(":giverOrReceiver", $giverOrReceiver, PDO::PARAM_INT);
		
		$result = $statement->execute();
		
		return $result;
	}
	

    /**
     * @param $conversationID
     * @return bool (True if current the user is the receiver)
     */
    function checkIfReceiver($conversationID)
    {
        $currentUser = $this->getUserID();
		
		$statement = $this->db->prepare("SELECT COUNT(*) AS `count` 
								FROM Conversation
								WHERE ConversationID = :conversationID 
								AND FK_User_ReceiverID = :userID;");
								
		$statement->bindValue(':conversationID', $conversationID, PDO::PARAM_INT);		
		$statement->bindValue(':userID', $currentUser, PDO::PARAM_INT);	
			
		$numberRows = count($statement->execute()->fetchColumn());
		
		return $numberRows > 0;
	}

}
?>