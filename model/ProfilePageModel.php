<?php
/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 03/03/2017
 * Time: 10:22
 */
namespace Wastetopia\Model;
use PDO;
use Wastetopia\Model\DB;

/**
 * Class ProfilePageModel - Functions for anything on profile page
 * @package Wastetopia\Model
 */
class ProfilePageModel
{
    /**
     * ProfilePageModel constructor.
     * @param @userID ID of user whose profile you're trying to view
     */
    public function __construct($userID)
    {
        $this->db = DB::getDB();
        $this->userID = $userID;

    }
    /**
     * Returns the ID of the user whose profile you're trying to view
     * @return int
     */
    private function getUserID()
    {
        return $this->userID;
    }
    


    /**
     * Gets all the listings the current user has put up
     * Can then use getStateOfListingTransactions() to check if the state of the transactions for those listings
     * @return mixed
     */
    function getUserListings()
    {
        $userID = $this->getUserID();
        $statement = $this->db->prepare("
            SELECT `Listing`.*
            FROM `Listing`
            JOIN `User` ON `User`.`UserID` = `Listing`.`FK_User_UserID`
            WHERE `User`.`UserID` = :userID
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Gets all the listings the current user is involved in transactions with (where they are the receiver)
     * Also gets the transactionID of those transactions
     * @return mixed
     */
    function getUserReceivingListings()
    {
        $userID = $this->getUserID();
        $statement = $this->db->prepare("
            SELECT `Listing`.*, `Transaction`.*, `ListingTransaction`.`Success`, `ListingTransaction`.`Sender_Hide`, `ListingTransaction`.`Receiver_Hide`
                FROM `Listing`
                JOIN `ListingTransaction` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
                JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
                JOIN `User` ON `User`.`UserID` = `Transaction`.`FK_User_UserID`
                WHERE `User`.`UserID` = :userID
		AND `Listing`.`Active` = 1;
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
	
   
	
    /**
    * Gets the pending offers made for user's items which they haven't seen yet (notification)
    * @return mixed 
    */
    function getUnseenPendingTransactions(){
	$userID = $this->getUserID();
        $statement = $this->db->prepare("
            SELECT `Transaction`.`TransactionID, `Listing`.`ListingID`
	    FROM `ListingTransaction`
	    JOIN `Listing` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
	    JOIN `Transaction` ON `Transaction`.`TransactionID` = ListingTransaction`.`FK_Transaction_TransactionID`
	    WHERE `ListingTransaction`.`Success` = 0
	    AND `ListingTransaction`.`Viewed` = 0
	    AND `Listing`.`FK_User_UserID` = :userID
	    AND `Listing`.`Active` = 1;
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchColumn();    
    }
	
	
    /**
    * Gets the total number of pending offers made for user's items which they haven't seen yet (notification)
    * @returns integer (total)
    */
    function getNumberOfUnseenPendingTransactions(){
	$userID = $this->getUserID();
        $statement = $this->db->prepare("
            SELECT COUNT(*) AS `Count`
	    FROM `ListingTransaction`
	    JOIN `Listing` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
	    WHERE `ListingTransaction`.`Success` = 0
	    AND `ListingTransaction`.`Viewed` = 0
	    AND `Listing`.`FK_User_UserID` = :userID
	    AND `Listing`.`Active` = 1;
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchColumn();    
    }
	
	
    /**
     * Gets all information about transactions for this listing.
     * If there are no results, this item has not been requested
     * Each transaction will have an ID and a success flag
     * @param $listingID
     * @return mixed
     */
    function getStateOfListingTransaction($listingID)
    {
        $statement = $this->db->prepare("
            SELECT `Transaction`.`TransactionID`, `ListingTransaction`.`Success`, `ListingTransaction`.`Sender_Hide`, `ListingTransaction`.`Receiver_Hide`
            FROM `Transaction`
            JOIN `ListingTransaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
            JOIN `Listing` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
            WHERE `Listing`.`ListingID` = :listingID
        ");
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Gets the name of the user requesting the transaction, and the quantity involved
     * @param $transactionID
     * @return mixed
     */
    function getDetailsFromTransactionID($transactionID){
        $statement = $this->db->prepare("
            SELECT `Transaction`.`FK_User_UserID` as `UserID`, `Transaction`.`Time_Of_Application`, `Transaction`.`Time_Of_Acceptance`,
                    `User`.`Forename`, `User`.`Surname`,
                    `ListingTransaction`.`Quantity`, 
                    `Listing`.`ListingID`
            FROM `Transaction`
            JOIN `User` ON `User`.`UserID` = `Transaction`.`FK_User_UserID`
            JOIN `ListingTransaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
            JOIN `Listing` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
            WHERE `Transaction`.`TransactionID` = :transactionID
        ");
        $statement->bindValue(":transactionID", $transactionID, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results[0];
    }

    /**
     * Gets the total number of completed listings the user has given
	 * If neither year nor month are specified then all completed listings are evaluated, regardless of date
	 * @param $year The subject year.
	 * @param $month The subject month
	 * @param $timespan How many months to evaluate, default 1
     * @return Integer
     */
    function getNumberOfCompletedGiving($year = -1, $month = -1, $timespan = 1)
    {
        $userID = $this->getUserID();
		$end_year = $year;
		$end_month = $month;
		if($year == -1 && $month == -1){
			$statement = $this->db->prepare("
				SELECT COUNT(*) as `Count`
				FROM `Listing`
				JOIN `ListingTransaction` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
				JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
				JOIN `User` ON `User`.`UserID` = `Listing`.`FK_User_UserID`
				WHERE `User`.`UserID` = :userID
				AND `ListingTransaction`.`Success` = 1;
			");
			$statement->bindValue(":userID", $userID, PDO::PARAM_INT);
			$statement->execute();
			return $statement->fetchColumn();
		}
		else if($year != -1 && $month != -1){
			if($month + $timespan >= 13){
				while($timespan >= 1){
					if($end_month == 12){
						$end_month = 1;
						$end_year = $end_year + 1;
						$timespan = $timespan - 1;
					}
					else{
						$end_month = $end_month + 1;
						$timespan = $timespan - 1;
					}
				}
			}
			else{
				$end_month = $end_month + $timespan;
			}
			
			$month = sprintf("%02d", $month);
			$end_month = sprintf("%02d", $end_month);
			
			$statement = $this->db->prepare("
				SELECT COUNT(*) as `Count`
				FROM `Listing`
				JOIN `ListingTransaction` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
				JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
				JOIN `User` ON `User`.`UserID` = `Listing`.`FK_User_UserID`
				WHERE `User`.`UserID` = :user_id
				AND `ListingTransaction`.`Success` = 1
				AND Time_Of_Acceptance >= :start_date
				AND Time_Of_Acceptance < :end_date;
			");
			
			$statement->bindValue(":user_id", $userID);
			$start_date = "" . $year . "-" . $month . "-01 00:00:00";
			$end_date = "" . $end_year . "-" . $end_month . "-01 00:00:00";
			$statement->bindValue(":start_date", $start_date, PDO::PARAM_STR);
			$statement->bindValue(":end_date", $end_date, PDO::PARAM_STR);
			
			$statement->execute();
			$errors = $statement->errorInfo();
			return $statement->fetchColumn() . " + errors: " . $errors[0] . $errors[1] . $errors[2] . "start_date: " . $start_date . "end_date: " . $end_date;
		}
		else{
			$error_log("Received failed");
			return false;
		}
		
    }

    /**
     * Gets the total number of completed listings the user has received
	 * If neither year nor month are specified then all completed listings are evaluated, regardless of date
	 * @param $year The subject year
	 * @param $month The subject month
	 * @param $timespan How many months to evaluate, default 1
     * @return Integer
     */
    function getNumberOfCompletedReceived($year = -1, $month = -1, $timespan = 1)
    {
		$userID = $this->getUserID();
		$end_year = $year;
		$end_month = $month;
		if($year == -1 && $month == -1){
			$statement = $this->db->prepare("
				SELECT COUNT(*) as `Count`
				FROM `Listing`
				JOIN `ListingTransaction` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
				JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
				WHERE `Transaction`.`FK_User_UserID` = :userID
				AND `ListingTransaction`.`Success` = 1;
			");
			$statement->bindValue(":userID", $userID, PDO::PARAM_INT);
			$statement->execute();
			return $statement->fetchColumn();
		}
		else if($year > -1 && $month > -1){
			if($month + $timespan >= 13){
				while($timespan >= 1){
					if($end_month == 12){
						$end_month = 1;
						$end_year = $end_year + 1;
						$timespan = $timespan - 1;
					}
					else{
						$end_month = $end_month + 1;
						$timespan = $timespan - 1;
					}
				}
			}
			else{
				$end_month = $end_month + $timespan;
			}
			$month = sprintf("%02d", $month);
			$end_month = sprintf("%02d", $end_month);
			$statement = $this->db->prepare("
				SELECT COUNT(*) as `Count`
				FROM `Listing`
				JOIN `ListingTransaction` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
				JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
				WHERE `Transaction`.`FK_User_UserID` = :user_id
				AND `ListingTransaction`.`Success` = 1
				AND Time_Of_Acceptance >= :start_date
				AND Time_Of_Acceptance < :end_date;
			");
			
			$statement->bindValue(":user_id", $userID);
			$start_date = "" . $year . "-" . $month . "-01 00:00:00";
			$end_date = "" . $end_year . "-" . $end_month . "-01 00:00:00";
			$statement->bindValue(":start_date", $start_date, PDO::PARAM_STR);
			$statement->bindValue(":end_date", $end_date, PDO::PARAM_STR);
			
			$statement->execute();
			$errors = $statement->errorInfo();
			return $statement->fetchColumn() . " + errors: " . $errors[0] . $errors[1] . $errors[2] . "start_date: " . $start_date . "end_date: " . $end_date;
		}
		else{
			return "Neither $year nor $month were equal to -1 and were not >= -1";
			$error_log("Received failed");
			return false;
		}
		
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
     * Gets all the listings the current user is watching
     * Can then use getStateOfListingTransactions() to check if the transaction should go in History or Currently Watching
     * @return mixed (listingID and WatchID)
     */
    function getWatchedListings($userID)
    {
        $statement = $this->db->prepare("
            SELECT `Listing`.`ListingID`, `Watch`.`WatchID`
            FROM `Listing`
            JOIN `Watch` ON `Listing`.`ListingID` = `Watch`.`FK_Listing_ListingID`
            JOIN `User` ON `User`.`UserID` = `Watch`.`FK_User_UserID`
            WHERE `User`.`UserID` = :userID
	    AND `Listing`.`Active` = 1;
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Deletes a listing from user's watch list
     * @param $watchID
     * @return bool
     */
    function deleteFromWatchList($listingID, $userID)
    {
 
        $statement = $this->db->prepare("
            DELETE
            FROM `Watch`
            WHERE `Watch`.`FK_Listing_ListingID` = :listingID
	    AND `Watch`.`FK_User_UserID` = :userID;
        ");
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
	$statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->execute();
        return True;
    }

    /**
     * Adds a listing to a user's watch list
     * @param $listingID
     * @return bool
     */
    function addToWatchList($listingID, $userID)
    {

        $statement = $this->db->prepare("
            INSERT
            INTO `Watch`(`FK_User_UserID`, `FK_Listing_ListingID`)
            VALUES(:userID, :listingID)
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->execute();
        return True;
    }
	
	
    /** 
    * Checks whether the given user has an ongoing request for the given listing
    * @param $listingID
    * @param $userID
    * @return bool (True if user is requesting the listing)
    */
    function isRequesting($listingID, $userID){

        $statement = $this->db->prepare("
            SELECT COUNT(*) AS `Count`
	    FROM `ListingTransaction`
	    JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
		JOIN `Listing` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
	    WHERE `ListingTransaction`.`FK_Listing_ListingID` = :listingID
	    AND `Transaction`.`FK_User_UserID` = :userID
	    AND `ListingTransaction`.`Success` = 0;
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->execute();
	
        return $statement->fetchColumn() > 0;
	    
    }


	
    /** 
    * Returns 1 if the given user has rated the given transaction
    * @param $transactionID
    * @param $userID
    * @return int
    */
    function hasRated($transactionID, $userID){
	$statement = $this->db->prepare("
            SELECT COUNT(*) AS `Count`
	    FROM `ListingTransaction`
	    JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
	    WHERE `Transaction`.`FK_User_UserID` = :userID
	    AND `Transaction`.`TransactionID` = :transactionID
	    AND `ListingTransaction`.`Rated` = 1;
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->bindValue(":transactionID", $transactionID, PDO::PARAM_INT);
        $statement->execute();
	
        return $statement->fetchColumn() > 0;
    }
	
	
    /** 
    * Sets the Giver_Viewed or Receiver_Viewed flag to the given value for the given transactionID
    * @pram $giverOrReceiver - 1 for Giver_Viewed, 0 for Receiver_Viewed
    * @param $transactionID
    * @param $value
    * @return bool
    */
    function setListingTransactionHiddenFlag($giverOrReceiver, $transactionID,  $value){

	// PDO statement for setting the Giver_Viewed flag
	$statementOption1 = $this->db->prepare("
            UPDATE `ListingTransaction`
	    SET `ListingTransaction`.`Sender_Hide` = :value
	    WHERE `ListingTransaction`.`FK_Transaction_TransactionID` = :transactionID
        ");
	
	// PDO statement for setting the Receiver_Viewed flag    
	$statementOption2 = $this->db->prepare("
            UPDATE `ListingTransaction`
	    SET `ListingTransaction`.`Receiver_Hide` = :value
	    WHERE `ListingTransaction`.`FK_Transaction_TransactionID` = :transactionID
        ");
	    
	// Choose which statement option to use    
	$statement = $giverOrReceiver ? $statementOption1 : $statementOption2;
		
	print_r($statement);
	    
        $statement->bindValue(":transactionID", $transactionID, PDO::PARAM_INT);
        $statement->bindValue(":value", $value, PDO::PARAM_INT);
        $statement->execute();
	
        return True;
	    
    }
	
	
    /**
    * Gets the password details for the given user)
    * @param $userID
    * @return array containing Password_Hash and Salt
    */
    function getPasswordDetails($userID){
	$statement = $this->db->prepare("
            SELECT `User`.`Password_Hash`, `User`.`Salt`
	    FROM `User`
	    WHERE `User`.`UserID` = :userID
        ");
	
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        
        $statement->execute();
	
        return $statement->fetchAll(PDO::FETCH_ASSOC)[0];    
	    
    }


    /** 
    * Hashes new password and stores it in the DB for the current user
    * @param $newPassword
    * @return bool
    */
    function updatePassword($newPassword){
	    $userID = $this->getUserID();
	    
	    $newSalt = $this->generateSalt();
	    $newPasswordHash = hash('sha256', $newSalt.$newPassword);	
	    
	    $statement = $this->db->prepare("
	    	UPDATE `User`
		SET `User`.`Password_Hash` = :hash , `User`.`Salt` = :salt
		WHERE `User`.`UserID` = :userID
	    ");
	    
	    $statement->bindValue(":hash", $newPasswordHash, PDO::PARAM_INT);
	    $statement->bindValue(":salt", $newSalt, PDO::PARAM_INT);
	    $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
	    
	    $statement->execute();
	    
	    return True;
    }
	
	
    /**
    * Gets the email of the given user
    * @param $userID
    * @return string
    */
    function getUserEmail($userID){
	 $statement = $this->db->prepare("
		SELECT `User`.`Email_Address`
		FROM `User`
		WHERE `User`.`UserID` = :userID
	    ");

	    $statement->bindValue(":userID", $userID, PDO::PARAM_INT);

	    $statement->execute();
	    $email = $statement->fetchColumn();
	    return $email;   
    }
	
	
    /**
     * Generates a random Salt string (in Hexadecimal) between 30 and 40 bytes in length
     * @param $min (default 30)
     * @param $max (default 40)
     * @return string
     */
    function generateSalt($min = 30, $max = 40){
        $salt = random_bytes(mt_rand(30, 40));
        return bin2hex($salt);
    }	
	
	
   /**
   * Resets the verification code and active flag for the given user
   * @param $email
   * @return bool
   */
    function resetAccount($userID, $email){
	// Change verification code
	$newVerificationCode = $this->generateSalt(); 
	    error_log("New code: ".$newVerificationCode);
	    
	$statement = $this->db->prepare("
		UPDATE 	`User`
		SET `Verification_Code` = :code, `Active` = 0, `Email_Address` = :email
		WHERE `UserID` = :userID
	");
	    
	 $statement->bindValue(":code", $newVerificationCode, PDO::PARAM_INT);
	 $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
	 $statement->bindValue(":email", $email, PDO::PARAM_STR);
	    
	 $statement->execute();
	 return True;
    }
	

    /**
    * Replaces the user's profile picture with that at the given URL
    * @param $url
    * @return bool
    */
    function changeProfilePicture($url){
        $userID = $this->getUserID();

        $statement = $this->db->prepare("
            UPDATE `User`
            SET `User`.`Picture_URL` = :url
            WHERE `User`.`UserID` = :userID
        ");

        $statement->bindValue(":url", $url, PDO::PARAM_STR);
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);

        $statement->execute();
        return True;
    }
}
