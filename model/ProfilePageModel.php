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
     * Gets all detail from the User table for this user
     * @return mixed
     */
    function getUserDetails(){
        $userID = $this->getUserID();
        $statement = $this->db->prepare("
        SELECT * 
        FROM `User` 
        WHERE `UserID` = :userID
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results[0];
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
     * Can then use getStateOfListingTransactions() to check if the transaction should go in History or Currently Watching
     * @return mixed
     */
    function getUserReceivingListings()
    {
        $userID = $this->getUserID();
        $statement = $this->db->prepare("
            SELECT `Listing`.*
                FROM `Listing`
                JOIN `ListingTransaction` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
                JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
                JOIN `User` ON `User`.`UserID` = `Transaction`.`FK_User_UserID`
                WHERE `User`.`UserID` = 6;
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
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
            SELECT `Transaction`.`TransactionID`, `ListingTransaction`.`Success`
            FROM `Transaction`
            JOIN `ListingTransaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
            JOIN `Listing` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
            WHERE `Listing`.`ListingID` = :listingID
        ");
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /* Gets the name of the user requesting the transaction, and the quantity involved
     * @param $transactionID
     * @return mixed
     */
    function getDetailsFromTransactionID($transactionID){
        $statement = $this->db->prepare("
            SELECT `Transaction`.`FK_User_UserID`,
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
     * @return Ineteger
     */
    function getNumberOfCompletedGiving()
    {
        $userID = $this->getUserID();

        $statement = $this->db->prepare("
            SELECT COUNT(*) as `Count`
            FROM `Listing`
            JOIN `ListingTransaction` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
            JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
            JOIN `User` ON `User`.`UserID` = `Listing`.`FK_User_UserID`
            WHERE `User`.`UserID` = :userID;
            AND `ListingTransaction`.`Success` = 1;  
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchColumn();
    }


    /**
     * Gets the total number of completed listings the user has received
     * @return Ineteger
     */
    function getNumberOfCompletedReceived()
    {
        $userID = $this->getUserID();

        $statement = $this->db->prepare("
            SELECT COUNT(*) as `Count`
            FROM `Listing`
            JOIN `ListingTransaction` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
            JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
            JOIN `User` ON `User`.`UserID` = `Transaction`.`FK_User_UserID`
            WHERE `User`.`UserID` = :userID;
            AND `ListingTransaction`.`Success` = 1;
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchColumn();
    }


    /**
     * Gets all the listings the current user is watching
     * Can then use getStateOfListingTransactions() to check if the transaction should go in History or Currently Watching
     * @return mixed (listingID and WatchID)
     */
    function getWatchedListings()
    {
        $userID = $this->getUserID();
        $statement = $this->db->prepare("
            SELECT `Listing`.`ListingID`, `Watch`.`WatchID`
            FROM `Listing`
            JOIN `Watch` ON `Listing`.`ListingID` = `Watch`.`FK_Listing_ListingID`
            JOIN `User` ON `User`.`UserID` = `Watch`.`FK_User_UserID`
            WHERE `User`.`UserID` = :userID
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Deletes a listing from user's watch list
     * @param $watchID
     */
    function deleteWatchListing($watchID)
    {
        $userID = $this->getUserID();
        $statement = $this->db->prepare("
            DELETE
            FROM `Watch`
            WHERE `Watch`.`WatchID` = :watchID
        ");
        $statement->bindValue(":watchID", $watchID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Adds a listing to a user's watch list
     * @param $listingID
     */
    function addToWatchList($listingID)
    {
        $userID = $this->getUserID();
        $statement = $this->db->prepare("
            INSERT
            INTO `Watch`(`FK_User_UserID`, `FK_Listing_ListingID`)
            VALUES(:userID, :listingID)
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->execute();
        return;
    }


    /**
     * Returns the details needed for display on the profile page given the listing ID
     *
     * @param $listingID
     * @return mixed
     */
    function getCardDetails($listingID){
        try {
            $statement = $this->db->prepare("
            SELECT `Listing`.`ListingID`, `Listing`.`Quantity`, `Listing`.`Time_Of_Creation`,
                    `Item`.`Name`, `Item`.`Description`, 
                    `Location`.`Post_Code`,
                    `User`.`UserID`, `User`.`Forename`, `User`.`Surname`
            FROM `Listing`
            JOIN `User` ON `Listing`.`FK_User_UserID` = `User`.`UserID`
            JOIN `Location` ON `Listing`.`FK_Location_LocationID` = `Location`.`LocationID`
            JOIN `Item` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            WHERE `Listing`.`ListingID` = :listingID;
            ");
            $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
            $statement->execute();
            $results =  $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
        print_r("FROM MODEL:: ");
        print_r($listingID);
        print_r($results);
        return $results[0];
    }


    /**
     * Returns the default image for this listing (if there is one)
     * @param $listingID
     * @return mixed
     */
    function getDefaultImage($listingID){
        $statement = $this->db->prepare("
            SELECT `Image`.`Image_URL`, 
            FROM `IMAGE`
            JOIN `ItemImage` ON `ItemImage`.`FK_Image_ImageID` = `Image`.`ImageID`
            JOIN `Item` ON `ItemImage`.`FK_Item_ItemID` = `Item`.`ItemID`
            JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            WHERE `Listing`.`ListingID` = :listingID
            AND `Image`.`Is_Default` = 1;
        ");
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results[0];
    }
}
