<?php

//BASICALLY UNTESTED

namespace Wastetopia\Model;
use PDO;
use Wastetopia\Model\DB;
use Wastetopia\Model\ItemModel;
use Wastetopia\Model\ListingModel;



class RequestModel
{
	function __construct()
	{
		$this->db = DB::getDB();
		$this->item_model = new ItemModel();
		$this->listing_model = new ListingModel();
	}

    /**
     * Returns the ID of the user currently logged in
     * @return string
     */
    function getUserID()
    {
//        $reader = new UserCookieReader();
//        return $reader->get_user_id();
        return 6; //Hardcoded for now
    }

	/**
     * Returns the ID of the last thing inserted in the database
     * @return int
     */
    private function getLastInsertID()
    {
        $statement = $this->db->prepare("
            SELECT LAST_INSERT_ID()
         ");
        $statement->execute();
        return $statement->fetchColumn();
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
	
	/**
	 * Makes a new request for the specified quantity of the specified item
	 * @return bool
	 */
	
	function requestItem($listing_id, $quantity=1){
		$currentUser = $this->getUserID();
		//first make the transaction
		$item_quantity = $this->item_model->getItemInfo(-1, $listing_id)["Quantity"];
		if($quantity > $item_quantity){
			return false;
		}
		$statement1 = $this->db->prepare("
			INSERT INTO Transaction(FK_User_UserID)
			VALUES(:userID);
		");
		$statement1->bindValue(":userID", $currentUser, PDO::PARAM_INT);
		$statement1->execute();
		
		$transaction_id = $this->getLastInsertID();
		
		//then link the transaction to the listing
		$statement2 = $this->db->prepare("
			INSERT INTO ListingTransaction(FK_Listing_ListingID, FK_Transaction_TransactionID, Quantity)
			VALUES (:listing_id, :transaction_id, :quantity);
		");
		$statement2->bindValue(":listing_id", $listing_id, PDO::PARAM_INT);
		$statement2->bindValue(":transaction_id", $transaction_id, PDO::PARAM_INT);
		$statement2->bindValue(":quantity", $quantity, PDO::PARAM_INT);
		$statement2->execute();
		return true;
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
		$item_quantity = $this->listing_model->getListingInfo($listing_id)["Quantity"];
		if($item_quantity < $quantity){
			return false;
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
		return true;
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
	 * Set the request as having been viewed
	 * @return void
	 */
	
	function setViewed($listing_id, $transaction_id, $new_value=1){
		$statement = $this->db->prepare("
			UPDATE ListingTransaction
			SET Viewed = :new_value
			WHERE FK_Listing_ListingID = :listing_id
			AND FK_Transaction_TransactionID = :transaction_id;
		");
		$statement->bindValue(":new_value", $new_value, PDO::PARAM_INT);
		$statement->bindValue(":listing_id", $listing_id, PDO::PARAM_INT);
		$statement->bindValue(":transaction_id", $transaction_id, PDO::PARAM_INT);
		$statement->execute();
	}
	
	/**
	 * Change the quantity of the item being offered
	 * @return bool
	 */
	
	function renewListing($listing_id, $new_quantity){
		//create the new listing of the specified quantity
		$listing_info = $this->listing_model->getListingInfo($listing_id);
		$item_info = $this->item_model->getItemInfoFromItemID($listing_info["FK_Item_ItemID"]);
		$statement0 = $this->db->prepare("
			INSERT INTO Item(Name, Description,Use_By)
			VALUES(:name, :description, :use_by);
		");
		$statement0->bindValue(":name", $item_info["Name"], PDO::PARAM_STR);
		$statement0->bindValue(":description", $item_info["Description"], PDO::PARAM_STR);
		$statement0->bindValue(":use_by", $item_info["Use_By"], PDO::PARAM_STR);
		$statement0->execute();
		$new_item_id = $this->getLastInsertID();
		
		$statement = $this->db->prepare("
			INSERT INTO Listing(FK_Location_LocationID, FK_Item_ItemID, FK_User_UserID, Quantity)
			VALUES (:location, :item_id, :user_id, :new_quantity);
		");
		$statement->bindValue(":location", $listing_info["FK_Location_LocationID"]);
		$statement->bindValue(":item_id", $new_item_id);
		$statement->bindValue(":user_id", $listing_info["FK_User_UserID"]);
		$statement->bindValue(":new_quantity", $new_quantity);
		$statement->execute();
		//remove the old listing so that the new listing replaces it
		$this->withdrawListing($listing_id);
		return true;
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
		return true;
	}
	
	/**
	 * Reject some request for an item
	 * @return void
	 */
	
	function rejectRequest($listing_id, $transaction_id){
		$statement = $this->db->prepare("
			UPDATE ListingTransaction
			SET Success = 2
			WHERE FK_Listing_ListingID = :listing_id
			AND FK_Transaction_TransactionID = :transaction_id;
		");
		$statement->bindValue(":listing_id", $listing_id, PDO::PARAM_INT);
		$statement->bindValue(":transaction_id", $transaction_id, PDO::PARAM_INT);
		$statement->execute();
	}
	
	function withdrawRequest($transaction_id){
		$statement = $this->db->prepare("
			DELETE FROM Transaction 
			WHERE TransactionID = :transaction_id;
		");
		$statement->bindValue(":transaction_id"< $transaction_id, PDO::PARAM_INT);
		$statement->execute();
	}
}

?>

