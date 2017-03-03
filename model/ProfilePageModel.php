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
    function retrieveUserDetails(){
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
     * @return mixed
     */
    function retriveUserListings()
    {
        $userID = $this->getUserID();

        $statement = $this->$db->prepare("
            SELECT * 
            FROM `Listing`
            JOIN `User` ON `User`.`UserID` = `Listing`.`FK_User_UserID`
            WHERE `User`.`UserID` = :userID
        ");

        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);

    }


    /**
     * Gets all information about transactions for this listing
     * Each transaction will have a time stamp and a success flag
     * @param $listingID
     * @return mixed
     */
    function getStateOfListingTransactions($listingID)
    {
        $userID = $this->getUserID();

        $statement = $this->$db->prepare("
            SELECT `Transaction`.`FK_User_UserID`, `Transaction`.`Time_Of_Transaction`
                    `Listing Transaction`.`Quantity`, `Listing Transaction`.`Success`
            FROM `Transaction`
            JOIN `Listing Transaction` ON `Transaction`.`TransactionID` = `Listing Transaction`.`FK_Transaction_TransactionID`
            JOIN `Listing` ON `Listing`.`ListingID` = `Listing Transaction`.`FK_Listing_ListingID`
            WHERE `Listing`.`ListingID` = :listingID
        ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}