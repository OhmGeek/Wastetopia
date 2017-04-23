<?php

//BASICALLY UNTESTED

namespace Wastetopia\Model;
use PDO;
use Wastetopia\Model\DB;
use Wastetopia\Model\ItemModel;
use Wastetopia\Model\ListingModel;
use Wastetopia\Model\UserCookieReader;
use Wastetopia\Model\MessageModel;
use Wastetopia\Controller\MessageController;


class RequestModel
{
	function __construct()
	{
		$this->db = DB::getDB();
		$this->item_model = new ItemModel();
		$this->listing_model = new ListingModel();
		$this->messageModel = new MessageModel();
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
     * Returns the ID of the last incomplete transaction the $userID made for the $listingID
     * @param $listingID
     * @param $userID
     * @return int
     */	
   function getTransactionIDFromListingID($listingID){
	   $userID = $this->getUserID();
	   $statement = $this->db->prepare("
            SELECT `Transaction`.`TransactionID`
	    FROM `Transaction`
	    JOIN `ListingTransaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
	    WHERE `Transaction`.`FK_User_UserID` = :userID	
	    AND `ListingTransaction`.`FK_Listing_ListingID` = :listingID
	    AND `ListingTransaction`.`Success` = 0
	    ORDER BY `Transaction`.`Time_Of_Application` DESC;
         ");
	$statement->bindValue(":userID", $userID, PDO::PARAM_INT);    
	$statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);   
        $statement->execute();
	$results = $statement->fetchAll(PDO::FETCH_ASSOC);
	$result = $results["0"];   
        return $result["TransactionID"];
	   
   }
	
	/**
     * Returns the ID of the last transaction the User made
     * @param $userID
     * @return int
     */
    private function getLastTransactionID($userID)
    {

        $statement = $this->db->prepare("
            SELECT `Transaction`.`TransactionID`
	    FROM `Transaction`
	    WHERE `Transaction`.`FK_User_UserID` = :userID	    
	    ORDER BY `Transaction`.`Time_Of_Application` DESC;
         ");
	$statement->bindValue(":userID", $userID, PDO::PARAM_INT);    
        $statement->execute();
	$results = $statement->fetchAll(PDO::FETCH_ASSOC);
	$result = $results["0"];   
        return $result["TransactionID"];
    }
	
	/**
     * Returns the ID of the last item inserted with the given parameters
     * @param $name
     * @param $useBy
     * @param $description
     * @return int
     */
    private function getLastItemID($name, $useBy, $description)
    {
        $statement = $this->db->prepare("
            SELECT `Item`.`ItemID`
	    FROM `Item`
	    WHERE `Item`.`Name` = :name
	    AND `Item`.`Use_By` = STR_TO_DATE(:useBy, '%e %M, %Y')
	    AND `Item`.`Description` = :description
	    ORDER BY `Item`.`ItemID` DESC;
         ");
	    
	$statement->bindValue(":name", $name, PDO::PARAM_STR); 
	$statement->bindValue(":useBy", $useBy, PDO::PARAM_STR); 
	$statement->bindValue(":description", $description, PDO::PARAM_STR);     
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC)["0"];
	return $results["ItemID"];    
    }
	
	

    	/**
     * Returns the ID of the last item inserted with the given parameters
     * @param $name
     * @param $useBy
     * @param $description
     * @return int
     */
    private function getLastListingID($itemID, $locationID, $userID)
    {
        $statement = $this->db->prepare("
            SELECT `Listing`.`ListingID`
	    WHERE `FK_Item_ItemID` = :itemID
	    AND `FK_User_UserID` = :userID
	    AND `FK_Location_LocationID` = :locationID
	    ORDER BY ListingID DESC
         ");
	    
	$statement->bindValue(":itemID", $itemID, PDO::PARAM_STR); 
	$statement->bindValue(":userID", $userID, PDO::PARAM_STR); 
	$statement->bindValue(":locationID", $locationID, PDO::PARAM_STR);     
        $statement->execute();
	    
        $results = $statement->fetchAll(PDO::FETCH_ASSOC)["0"];
	return $results["ListingID"];    
    }
	
	
	/**
	 * Returns the quantity of the item requested 
	 * @return int
	 */
	function getRequestQuantity($listing_id, $transaction_id){
		$statement = $this->db->prepare("
			SELECT Quantity
			FROM ListingTransaction 
			WHERE FK_Listing_ListingID = :listing_id
			AND FK_Transaction_TransactionID = :transaction_id;
		");
		$statement->bindValue(":listing_id", $listing_id, PDO::PARAM_INT);
		$statement->bindValue(":transaction_id", $transaction_id, PDO::PARAM_INT);
		$statement->execute();
		return $statement->fetchColumn();
	}

	
	//Add something for if item is in watchList, need to remove it
	/**
	 * Makes a new request for the specified quantity of the specified item
	 * @return bool
	 */
	
	function requestItem($listing_id, $quantity=1){
		$currentUser = $this->getUserID();
		//first make the transaction
		$item_quantity = $this->item_model->getItemInfoFromListingID($listing_id)["0"]["Quantity"];
		print_r($this->item_model->getItemInfoFromListingID($listing_id));
		print_r($item_quantity);
		print_r("Quantity ".$quantity);
		if($quantity > $item_quantity){
			return False;
		}
		$statement1 = $this->db->prepare("
			INSERT INTO Transaction(FK_User_UserID)
			VALUES(:userID);
		");
		$statement1->bindValue(":userID", $currentUser, PDO::PARAM_INT);
		$statement1->execute();
		
		$transaction_id = $this->getLastTransactionID($currentUser);
		print_r("Transaction: ".$transaction_id);
		
		//then link the transaction to the listing
		$statement2 = $this->db->prepare("
			INSERT INTO ListingTransaction(FK_Listing_ListingID, FK_Transaction_TransactionID, Quantity)
			VALUES (:listing_id, :transaction_id, :quantity);
		");
		$statement2->bindValue(":listing_id", $listing_id, PDO::PARAM_INT);
		$statement2->bindValue(":transaction_id", $transaction_id, PDO::PARAM_INT);
		$statement2->bindValue(":quantity", $quantity, PDO::PARAM_INT);
		$statement2->execute();
		
		// Create conversation and send first message
		$conversationIDs = $this->messageModel->getConversationIDFromListing($listing_id);
		if (count($conversationIDs) == 0){
		   // Create the conversation
		   $conversationModel = new ConversationListModel();
		   $conversationModel->createConversation($listing_id);
		   $conversationIDs = $this->messageModel->getConversationIDFromListing($listing_id);
		   $conversationID = $conversationIDs[0];
			$conversationID = $conversationID["ConversationID"];
	           $messageController = new MessageController();
		   $messageController->sendMessage($conversationID, "I am interested in your requesting your item!");		
		}
		
		return True;
	}
	
	/**
	 * Acepts the request, optionally changing the quantity given away
	 * @return bool
	 */
	
	function confirmRequest($listing_id, $transaction_id, $quantity = -1){
		//if quantity unchanged, then find what the original quantity was
		if($quantity == -1){
			$quantity = $this->getRequestQuantity($listing_id, $transaction_id);
		}
		$item_quantity = $this->listing_model->getListingInfo($listing_id)["0"]["Quantity"];
		if($item_quantity < $quantity){
			//return false; 
			// Assume the user knows how much they said they're giving away 
			// Maybe they just couldn't be bothered increasing the listing quantity but actually had more to give away
			// Set the quantity to equal max available so listing quantity will never go below 0 in the DB
			$quantity = $item_quantity; 
		}
		// first update the listing to take into account the quantity being taken
		$statement1 = $this->db->prepare("
			UPDATE Listing
			SET Quantity = :new_quantity
			WHERE ListingID = :listing_id;
		");
		$statement1->bindValue(":new_quantity", $item_quantity - $quantity, PDO::PARAM_INT);
		$statement1->bindValue(":listing_id", $listing_id, PDO::PARAM_INT);
		$statement1->execute();
		//then update ListingTransaction to aknowledge the acceptance
		$statement2 = $this->db->prepare("
			UPDATE ListingTransaction
			SET Success = 1, Quantity = :quantity
			WHERE FK_Listing_ListingID = :listing_id
			AND FK_Transaction_TransactionID = :transaction_id;
		");
		$statement2->bindValue(":listing_id", $listing_id, PDO::PARAM_INT);
		$statement2->bindValue(":transaction_id", $transaction_id, PDO::PARAM_INT);
		$statement2->bindValue(":quantity", $quantity, PDO::PARAM_INT);
		$statement2->execute();
		// then enter the acceptance time into the Transaction
		$statement3 = $this->db->prepare("
			UPDATE Transaction 
			SET Time_Of_Acceptance = NOW() 
			WHERE TransactionID = :transaction_id;
		");
		$statement3->bindValue(":transaction_id", $transaction_id, PDO::PARAM_INT);
		$statement3->execute();
		return True;
	}
	
	/**
	 * Returns all unique identifiers for the requests associated with the user, optionally only the unviewed requests
	 * optionally specify a specific listing to get requests for
	 * optionally specify unviewed_only for whether to get all regardless of whether they've been viewed(0)
	 * only the unviewed requests(1)
	 * only the viewed requests(2)
	 * @return associative array
	 */
	
	function getRequestIDPairsForUser($unviewed_only = 1, $listing_id = -1){
		$currentUser = $this->getUserID();
		if($listing_id == -1){
			$statement = $this->db->prepare("
				SELECT FK_Listing_ListingID, FK_Transaction_TransactionID
				FROM ListingTransaction
				JOIN Listing ON Listing.ListingID = ListingTransaction.FK_Listing_ListingID
				WHERE Listing.FK_User_UserID = :user_id
				:viewed_string
				AND Success = 0;
			");
			$statement->bindValue(":user_id", $currentUser, PDO::PARAM_INT);
		}
			
		else{
			$statement = $this->db->prepare("
				SELECT FK_Listing_ListingID, FK_Transaction_TransactionID
				FROM ListingTransaction
				JOIN Listing ON Listing.ListingID = ListingTransaction.FK_Listing_ListingID
				WHERE Listing.FK_User_UserID = :user_id
				:viewed_string
				AND Success = 0
				AND ListingTransaction.FK_Listing_ListingID = :listing_id;
			");
			$statement->bindValue(":user_id", $currentUser, PDO::PARAM_INT);
			$statement->bindValue(":listing_id", $listing_id, PDO::PARAM_INT);
		}
		if($unviewed_only == 0){
			$statement->bindValue(":viewed_string", "", PDO::PARAM_STR);
		}
		else if($unviewed_only == 1){
			$statement->bindValue(":viewed_string", "AND Viewed = 0", PDO::PARAM_STR);
		}
		else if($unviewed_only == 2){
			$statement->bindValue(":viewed_string", "AND Viewed = 1", PDO::PARAM_STR);
		}
		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	
	/**
	 * Change the quantity of the item being offered
	 * @return bool
	 */
	
	function renewListing($listing_id, $new_quantity, $new_use_by_date){
		//create the new listing of the specified quantity
		$listing_info = $this->listing_model->getListingInfo($listing_id)["0"];
		$item_info = $this->item_model->getItemInfoFromItemID($listing_info["FK_Item_ItemID"])["0"];
		
		$name = $item_info["Name"];
		
		$description = $item_info["Description"];
		

		$statement0 = $this->db->prepare("
			INSERT INTO Item(Name, Description,Use_By)
			VALUES(:name, :description, STR_TO_DATE(:use_by, '%e %M, %Y'));
		");
		$statement0->bindValue(":name", $name, PDO::PARAM_STR);
		$statement0->bindValue(":description", $description, PDO::PARAM_STR);
		$statement0->bindValue(":use_by", $new_use_by_date, PDO::PARAM_STR);
		$statement0->execute();
		
		$new_item_id = $this->getLastItemID($name, $new_use_by_date, $description);
		
		//adding item tags
		$statement01 = $this->db->prepare("
			INSERT INTO ItemTag(FK_Item_ItemID, FK_Tag_TagID)
			SELECT :new_item_id, ItemTag.FK_Tag_TagID
			FROM ItemTag WHERE FK_Item_ItemID = :old_item_id;
		");
		$statement01->bindValue(":new_item_id", $new_item_id, PDO::PARAM_INT);
		$statement01->bindValue(":old_item_id", $listing_info["FK_Item_ItemID"], PDO::PARAM_INT);
		$statement01->execute();
		
		
		// adding item's images - item may now how two default images
		$statement02 = $this->db->prepare("
			INSERT INTO ItemImage(FK_Item_ItemID, FK_Image_ImageID, Is_Default)
			SELECT :new_item_id, FK_Image_ImageID, Is_Default
			FROM ItemImage WHERE FK_Item_ItemID = :old_item_id;
		");
		$statement02->bindValue(":new_item_id", $new_item_id, PDO::PARAM_INT);
		$statement02->bindValue(":old_item_id", $listing_info["FK_Item_ItemID"], PDO::PARAM_INT);
		$statement02->execute();

		
		$statement = $this->db->prepare("
			INSERT INTO Listing(FK_Location_LocationID, FK_Item_ItemID, FK_User_UserID, Quantity)
			VALUES (:location, :item_id, :user_id, :new_quantity);
		");
		$statement->bindValue(":location", $listing_info["FK_Location_LocationID"]);
		$statement->bindValue(":item_id", $new_item_id);
		$statement->bindValue(":user_id", $listing_info["FK_User_UserID"]);
		$statement->bindValue(":new_quantity", $new_quantity);
		$statement->execute();
		

		//print_r("Withdrawn old listing");
		$new_listing_id = $this->getLastListingID($new_item_id, $listing_info["FK_Location_LocationID"], $listing_info["FK_User_UserID"]);
	
		//print_r("Migrating pending transaction");			   
		// Move all pending transactions to new listing
		$this->migratePendingTransactions($listing_id, $new_listing_id);
		
		//make old listing inactive so that the new listing replaces it
		$this->withdrawListing($listing_id);
		
		return $new_listing_id;
	}
	
	/**
	 * 'Deletes' the listing
	 * @return bool
	 */
	
	function withdrawListing($listing_id){		
		$statement = $this->db->prepare("
			UPDATE Listing
			SET Active = 0
			WHERE ListingID = :listing_id;
		");
		$statement->bindValue(":listing_id", $listing_id, PDO::PARAM_INT);
		$statement->execute();
		
		
		// Get all pending transactions for this listing
		$pendingTransactions = $this->getPendingTransactionsForListing($listing_id);
		//print_r($pendingTransactions);
		
		// Reject each transaction
		foreach($pendingTransactions as $transactionArray){
	            $transaction_id = $transactionArray["TransactionID"];		
		    //print_r("Rejecting: ".$transaction_id);
		    $this->rejectRequest($listing_id, $transaction_id);
		}
		return True;
	}
	
					   
	/**
	* Gets all the pending transactions for a given listing
	* @param $listing_id
	* @return array of 'TransactionID's
	*/
	function getPendingTransactionsForListing($listing_id){
		$statement = $this->db->prepare("
			SELECT `ListingTransaction`.`FK_Transaction_TransactionID` AS `TransactionID`
			FROM `ListingTransaction`
			WHERE `ListingTransaction`.`FK_Listing_ListingID` = :listing_id
			AND `ListingTransaction`.`Success` = 0
		");
		$statement->bindValue(":listing_id", $listing_id, PDO::PARAM_INT);
		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);	
	}

					   
	/**
	 * Reject some request for an item
	 * @return void
	 */	
	function rejectRequest($listing_id, $transaction_id){
		$statement = $this->db->prepare("
			UPDATE `ListingTransaction`
			SET `ListingTransaction`.`Success` = 2
			WHERE `ListingTransaction`.`FK_Listing_ListingID` = :listing_id
			AND `ListingTransaction`.`FK_Transaction_TransactionID` = :transaction_id;
		");
		$statement->bindValue(":listing_id", $listing_id, PDO::PARAM_INT);
		$statement->bindValue(":transaction_id", $transaction_id, PDO::PARAM_INT);
		$statement->execute();
		return True;
	}
	
	function withdrawRequest($transaction_id){
		$statement = $this->db->prepare("
			DELETE FROM Transaction 
			WHERE TransactionID = :transaction_id;
		");
		$statement->bindValue(":transaction_id", $transaction_id, PDO::PARAM_INT);
		$statement->execute();
		return True;
	}
	
	
	/**
	* Links all the pending transactions for the old listing to the new listing
	* @param $old_listing_id
	* @param $new_listing_id
	* @return bool
	*/
	function migratePendingTransactions($old_listing_id, $new_listing_id){
		$statement = $this->db->prepare("
			UPDATE `ListingTransaction`
			SET `FK_Listing_ListingID` = :newListingID
			WHERE `FK_Listing_ListingID` = :oldListingID
			AND `ListingTransaction`.`Success` = 0;
		");
		$statement->bindValue(":newListingID", $new_listing_id, PDO::PARAM_INT);
		$statement->bindValue(":oldListingID", $old_listing_id, PDO::PARAM_INT);
		$statement->execute();
		return True;
	}
	
}

?>

