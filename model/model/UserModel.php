<?php

// Get users who you have a current conversation with

class UserModel
{
	function __construct()
	{
	    $this->db = DB::getDB();
	}
	
	function getCurrentUser(){
		return $_COOKIES["user_id"]; //WIll change when we decide how we're storing this
	}
	
	// Returns all conversations (with users) in which you are receiving an item with along with the number of unread messages you have from them
	function getConversationsReceiving()
	{

		$currentUser = 6; //COOKIES AREN'T WORKING
		
		$statement = $this->db->prepare("SELECT UserID, Forename, Surname, Conversation.ConversationID, Listing.ListingID, Item.Name,
									(
										SELECT COUNT(*)
										FROM Message
										WHERE Message.FK_Conversation_ConversationID = Conversation.ConversationID
										AND Giver_Or_Receiver = 0
										AND `Read` = 0
									) AS `count`
								FROM Message, Conversation, User, Listing, UserItem, Item
								WHERE Conversation.FK_User_ReceiverID = :userID 
								AND Listing.ListingID = Conversation.FK_Listing_ListingID
								AND Listing.FK_User_UserID = User.UserID
								AND Listing.FK_UserItem_UserItemID = UserItem.UserItemID AND UserItem.FK_Item_ItemID = Item.ItemID
								GROUP BY Conversation.ConversationID;");
		
		$statement->bindValue(':userID', $currentUser, PDO::PARAM_INT);
		
		$statement->execute();
		
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
	
	// Returns all conversations (with users) in which you are sending an item with along with the number of unread messages you have from them
    function getConversationsSending()
	{

		$currentUser = 6; //COOKIES AREN'T WORKING
		
		$statement = $this->db->prepare("SELECT UserID, Forename, Surname, Conversation.conversation_id, Listing.ListingID, Item.Name,
									(
										SELECT COUNT(*)
										FROM Message
										WHERE Message.FK_Conversation_ConversationID = Conversation.ConversationID
										AND Giver_Or_Receiver = 1
										AND `Read` = 0
									) AS `count`
								FROM Message, Conversation, User, Listing, UserItem, Item
								WHERE Conversation.FK_User_ReceiverID = User.UserID
								AND Listing.ListingID = Conversation.FK_Listing_ListingID
								AND Listing.FK_User_UserID = :userID
								AND Listing.FK_UserItem_UserItemID = UserItem.UserItemID 
								AND UserItem.FK_Item_ItemID = Item.ItemID
								GROUP BY Conversation.ConversationID;");
		
		$statement->bindValue(':userID', $currentUser, PDO::PARAM_INT);
		
		$statement->execute();
		
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}


	function createConversation($listingID)
	{

		
		$currentUser = 6; //COOKIES DON'T WORK YET

		$statement = $this->db->prepare("INSERT INTO Conversation (FK_User_ReceiverID, FK_Listing_ListingID)
									VALUES (:userID, :listingID)");

		$statement->bindValue(':userID', $currentUser, PDO::PARAM_INT);
		$statement->bindValue(':listingID', $listingID, PDO::PARAM_INT);
		
		$statement->execute();
		
	}
	
	// Returns the conversation_id of the conversation for a given listing
    // Returns IDs of Conversations where I am the receiver and the listing is that specified
	function getConversationFromListing($listingID)
	{
		
		$currentUser = 6; //GET FROM COOKIES
		
		$statement = $this->db->prepare("SELECT ConversationID 
									FROM Conversation
									WHERE FK_User_ReceiverID = :userID
									AND FK_Listing_ListingID = :listingID;");
		
		$statement->bindValue(":userID", $currentUser, PDO::PARAM_INT);
		$statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
		
		$result = $statement->execute()->fetchColumn();
		
		return $result;
	}


	function deleteConversation($conversationID)
    {

        $currentUser = 6; //COOKIES DON'T WORK YET

        $statement = $this->db->prepare("
                DELETE FROM `Conversation`
                WHERE `Conversation`.`ConversationID` = :conversationID
        ");

        $statement->bindValue(':conversationID', $conversationID, PDO::PARAM_INT);

        $statement->execute();

    }
}
	




?>