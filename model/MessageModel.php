<?php


namespace Wastetopia\Model;
use PDO;
use Wastetopia\Model\DB;
use Wastetopia\Model\UserCookieReader;


/**
 * Class MessageModel - Details of messages within a conversation
 * @package Wastetopia\Model
 */
class MessageModel
{
    /**
     * MessageModel constructor.
     */
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
    * Returns the ID of the conversation associated with the given listing and the current logged in user
    * @param $listingID
    * @return int ($conversationID)
    */
    function getConversationIDFromListing($listingID){
	$userID = $this->getUserID();

	$statement = $this->db->prepare("
		SELECT `Conversation`.`ConversationID`
		FROM `Conversation`
		JOIN `Listing` ON `Listing`.`ListingID` = `Conversation`.`FK_Listing_ListingID`
		WHERE (`Conversation`.`FK_User_ReceiverID` = :userID OR `Listing`.`FK_User_UserID` = :userID2)
		AND `Listing`.`ListingID` = :listingID;
	");
								
		$statement->bindValue(':userID',$userID, PDO::PARAM_INT);							
		$statement->bindValue(':userID2',$userID, PDO::PARAM_INT);
	        $statement->bindValue(':listingID', $listingID, PDO::PARAM_INT);

		$statement->execute();

		$results = $statement->fetchAll(PDO::FETCH_ASSOC);  
	        return $results;
	   
    }
	

    /**
     * Return all messages in the conversation betweeen you and another user
     * @param $conversationID
     * @return mixed
     */
    function getMessagesFromConversation($conversationID)
	{


		$statement = $this->db->prepare("SELECT Message.Content, Message.Time, User.UserID, User.Forename, User.Surname
								FROM Message, User, Conversation, Listing
								WHERE Message.FK_Conversation_ConversationID = :conversationID
								AND Conversation.ConversationID = :conversationID2
								AND ((Message.Giver_Or_Receiver = 1 
										AND User.UserID = Conversation.FK_User_ReceiverID)
									OR (Message.Giver_Or_Receiver = 0
										AND Conversation.FK_Listing_ListingID = Listing.ListingID
										AND Listing.FK_User_UserID = User.UserID))
								GROUP BY Message.MessageID
								ORDER BY Time ASC;");
								
		$statement->bindValue(':conversationID',$conversationID, PDO::PARAM_INT);							
		$statement->bindValue(':conversationID2',$conversationID, PDO::PARAM_INT);							

		$statement->execute();

		$results = $statement->fetchAll(PDO::FETCH_ASSOC);
	    return $results;
	}


    /**
     * Sets all messages in a conversation sent by otherUser to read`
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

		$statement->execute();
		
		$result = $statement->fetchColumn();
	    return $result;
	}


    /**
     * Gets the name of the other person in the conversation and the name of the item being given away
     * @param $conversationID
     * @return mixed (UserId, name, itemName)
     */
    function getConversationDetails($conversationID)
    {
	    //error_log("Getting conversation user details");
        $currentUser = $this->getUserID();

	$isReceiverOfItem = $this->checkIfReceiver($conversationID);
	    //error_log("Is receiver: ".$isReceiverOfItem);
	
	    if($isReceiverOfItem){
		    $statement = $this->db->prepare("		    		
			SELECT UserID, Forename, Surname, Item.Name
					FROM Conversation
					JOIN Listing ON Listing.ListingID = Conversation.FK_Listing_ListingID
					JOIN User ON User.UserID = Listing.FK_User_UserID
					JOIn Item ON Item.ItemID = Listing.FK_Item_ItemID
					WHERE Conversation.ConversationID = :conversationID;
		    ");
	    }else{
		$statement = $this->db->prepare("
		SELECT User.UserID, User.Forename, User.Surname, Item.Name
			FROM Conversation
			JOIN User ON User.UserID = Conversation.FK_User_ReceiverID
			JOIN Listing ON Listing.ListingID = Conversation.FK_Listing_ListingID
			JOIN Item ON ItemID = Listing.FK_Item_ItemID
			WHERE Conversation.ConversationID = :conversationID;
		");    
	    }
        

        $statement->bindValue(':conversationID', $conversationID, PDO::PARAM_INT);

        $statement->execute();

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
	    error_log("Results: ".json_encode($results));
	    return $results;
    }



    /**
     * Gets general details needed for side-panel on messages page
     * @param $conversationID
     * @return mixed
     */
    function getListingDetails($conversationID)
    {
        $statement = $this->db->prepare("

            SELECT `Item`.`Name` as ItemName, `Item`.`Use_By`,
            `Location`.`Name` as LocationName, `Location`.`Post_Code`,
            `Listing`.`ListingID`, `Listing`.`Active`
            FROM `Conversation`
            JOIN `Listing` ON `Listing`.`ListingID` = `Conversation`.`FK_Listing_ListingID`
            JOIN `Item` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            JOIN `Location` ON `Listing`.`FK_Location_LocationID` = `Location`.`LocationID`
            WHERE `Conversation`.`ConversationID` = :conversationID;
        ");

        $statement->bindValue(":conversationID", $conversationID, PDO::PARAM_INT);

        $statement->execute();

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
	    return $results;
    }

}

?>

