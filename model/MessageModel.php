<?php

namespace Wastetopia\Model;
use PDO;
use Wastetopia\Model\DB;

class MessageModel
{
	function __construct()
	{
		$this->db = DB::getDB();
	}

    /**
     * Returns the ID of the user currently logged in
     * @return string
     */
    function getUserID()
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
     * Sets all messages in a conversation sent by otherUser to read` (NOT SURE IF THIS IS CORRECT)
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


    /**
     * Gets the name of the other person in the conversation and the name of the item being given away
     * @param $conversationID
     * @return mixed (UserId, name, itemName)
     */
    function getConversationDetails($conversationID)
    {
        $currentUser = $this->getUserID();

        $statement = $this->db->prepare("
                                SELECT UserID, Forename, Surname, Item.Name
								FROM Conversation, User, Listing, Item
								WHERE Listing.ListingID = Conversation.FK_Listing_ListingID
								AND ((Conversation.FK_User_ReceiverID = User.UserID AND Listing.FK_User_UserID = :userID)
								  OR (Conversation.FK_User_ReceiverID = :userID2 AND Listing.FK_User_UserID = User.UserID))
								AND Listing.FK_Item_ItemID = Item.ItemID
								AND Conversation.ConversationID = :conversationID;
							");

        $statement->bindValue(':userID', $currentUser, PDO::PARAM_INT);
        $statement->bindValue(':userID2', $currentUser, PDO::PARAM_INT);
        $statement->bindValue(':conversationID', $conversationID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Gets general details needed for side-panel on messages page
     * @param $conversationID
     * @return mixed
     */
    function getListingDetails($conversationID)
    {
        $statement = $this->db->prepare("
            SELECT `Item`.`Name` as ItemName, `Item`.`Use_By_Date`
            `Location`.`Name` as LocationName, `Location`.Post_Code,
            `Listing`.`ListingID`
            FROM `Conversation`
            JOIN `Listing` ON `Listing`.`ListingID` = `Conversation`.`FK_Listing_ListingID`
            JOIN `Item` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            JOIN `Location` ON `Listing`.`FK_Location_LocationID` = `Location`.`LocationID`
            WHERE `Conversation`.`ConversationID` = :conversationID
        ");

        $statement->bindValue(":conversationID", $conversationID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Returns the default image for this listing (if there is one)
     * @param $listingID
     * @return mixed
     */
    function getDefaultImage($listingID){
        $statement = $this->db->prepare("
            SELECT `Image`.`Image_URL`, 
            FROM `Image`
            JOIN `ItemImage` ON `ItemImage`.`FK_Image_ImageID` = `Image`.`ImageID`
            JOIN `Item` ON `ItemImage`.`FK_Item_ItemID` = `Item`.`ItemID`
            JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            WHERE `Listing`.`ListingID` = :listingID
            AND `Image`.`Is_Default` = 1;
        ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchColumn();
    }

}
?>
