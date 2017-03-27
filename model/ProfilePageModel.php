<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 03/03/2017
 * Time: 10:22
 */
class ProfilePageModel
{

    /**
     * ProfilePageModel constructor.
     */
    public function __construct()
    {
        $this->db = DB::getDB();
    }


    /**
     * Returns the ID of the user currently logged in
     * @return int
     */
    private function getUserID()
    {
        $reader = new UserCookieReader();
        return $reader->get_user_id();
    }


    /**
     * Gets all detail from the User table for this user
     * @return mixed
     */
    function getUserDetails(){
        $userID = $this->getUserID();

        $statement = $this->$db->prepare("
            SELECT * 
            FROM `User`
            WHERE `UserID` = :userID
        ");

        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Gets all the listings the current user has put up
     * Can then use getStateOfListingTransactions() to check if the state of the transactions for those listings
     * @return mixed
     */
    function getUserListings()
    {
        $userID = $this->getUserID();

        $statement = $this->$db->prepare("
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

        $statement = $this->$db->prepare("
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
        $statement = $this->$db->prepare("
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

    /**
     * Gets the total number of completed listings the user has given
     * @return Ineteger
     */
    function getNumberOfCompletedGiving()
    {
        $userID = $this->getUserID();
        
        $statement = $this->$db->prepare("
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
        
        $statement = $this->$db->prepare("
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

        $statement = $this->$db->prepare("
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

        $statement = $this->$db->prepare("
            DELETE
            FROM `Watch`
            WHERE `Watch`.`WatchID` = :watchID
        ");

        $statement->bindValue(":watchID", $watchID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


}
