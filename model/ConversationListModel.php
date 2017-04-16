<?php
namespace Wastetopia\Model;
use PDO;
use Wastetopia\Model\DB;


/**
 * Class ConversationListModel - Details about general Conversations
 * @package Wastetopia\Model
 */
class ConversationListModel
{
    /**
     * ConversationListModel constructor.
     */
    function __construct()
	{
	    $this->db = DB::getDB();
	}


    /**
     * Returns the ID of the user currently logged in
     * @return string
     */
    private function getUserID()
    {
        //$reader = new UserCookieReader();
        //return $reader->get_user_id();
	return 6; //Hardcoded for now
    }


    /**
     * Gets all conversations (with users) in which you are receiving an item.
     * @return mixed (Array of conversation details, including number of unread messages for notifications)
     */
    function getConversationsReceiving()
	{

		$currentUser = $this->getUserID();
		$statement = $this->db->prepare("SELECT UserID, Forename, Surname, Conversation.ConversationID, Listing.ListingID, Item.Name,
									(
										SELECT COUNT(*)
										FROM Message
										WHERE Message.FK_Conversation_ConversationID = Conversation.ConversationID
										AND Giver_Or_Receiver = 0
										AND `Read` = 0
									) AS `count`
								FROM Message, Conversation, User, Listing, Item
								WHERE Conversation.FK_User_ReceiverID = :userID 
								AND Listing.ListingID = Conversation.FK_Listing_ListingID
								AND Listing.FK_User_UserID = User.UserID
								AND Listing.FK_Item_ItemID = Item.ItemID
								GROUP BY Conversation.ConversationID;");

		$statement->bindValue(':userID', $currentUser, PDO::PARAM_INT);

		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}


    /**
     * Gets all conversations in which you are sending an item
     * @return mixed (Array of conversation details, including unread messages for notifications)
     */
    function getConversationsSending()
	{

        $currentUser = $this->getUserID();

		$statement = $this->db->prepare("SELECT UserID, Forename, Surname, Conversation.ConversationID, Listing.ListingID, Item.Name,
									(
										SELECT COUNT(*)
										FROM Message
										WHERE Message.FK_Conversation_ConversationID = Conversation.ConversationID
										AND Giver_Or_Receiver = 1
										AND `Read` = 0
									) AS `count`
								FROM Message, Conversation, User, Listing, Item
								WHERE Conversation.FK_User_ReceiverID = User.UserID
								AND Listing.ListingID = Conversation.FK_Listing_ListingID
								AND Listing.FK_User_UserID = :userID
								AND Listing.FK_Item_ItemID = Item.ItemID 
								GROUP BY Conversation.ConversationID;");
		
		$statement->bindValue(':userID', $currentUser, PDO::PARAM_INT);
		
		$statement->execute();
		
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}


    /**
     * Gets the conversation associated with a given listing (where the current user is the receiver)
     * @param $listingID
     * @return mixed (the conversationID of any conversations involving this user)
     */
    function getConversationsFromListing($listingID)
    {

        $currentUser = $this->getUserID();

        $statement = $this->db->prepare("SELECT ConversationID 
									FROM Conversation
									WHERE FK_User_ReceiverID = :userID
									AND FK_Listing_ListingID = :listingID;");

        $statement->bindValue(":userID", $currentUser, PDO::PARAM_INT);
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);

        $result = $statement->execute()->fetchColumn();

        return $result;
    }


    /**
     * Creates a conversation for a given listing
     * @param $listingID
     */
    function createConversation($listingID)
	{

	    
        $currentUser = $this->getUserID();
		$statement = $this->db->prepare("INSERT INTO Conversation (FK_User_ReceiverID, FK_Listing_ListingID)
									VALUES (:userID, :listingID)");

		$statement->bindValue(':userID', $currentUser, PDO::PARAM_INT);
		$statement->bindValue(':listingID', $listingID, PDO::PARAM_INT);
		
		$statement->execute();
		
	}


    /**
     * Deletes a conversation and its associated messages
     * @param $conversationID
     */
    function deleteConversation($conversationID)
    {

        $currentUser = $this->getUserID();

        $statement = $this->db->prepare("
                DELETE FROM `Conversation`
                WHERE `Conversation`.`ConversationID` = :conversationID
        ");

        $statement->bindValue(':conversationID', $conversationID, PDO::PARAM_INT);

        $statement->execute();

    }


    /**
     * Gets the profile picture of the given user (Possibly will be moved to another model)
     * @param $userID
     * @return URL
     */
    function getUserImage($userID)
    {
        $statement = $this->db->prepare("
                                SELECT Picture_URL
                                FROM `User`
                                WHERE `User`.`UserID` = :userID
							");

        $statement->bindValue(':userID', $userID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchColumn();
    }

}

?>

