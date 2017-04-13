<?php
/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 04/04/2017
 * Time: 11:34
 */

namespace Wastetopia\Model;
use Wastetopia\Model\DB;
use PDO;

class CardDetailsModel
{


    /**
     * CardDetailsModel constructor.
     */
    public function __construct()
    {
        $this->db = DB::getDB();
    }

    function getUserID(){
        // Function from elsewhere
    }

    /**
     * Gets all detail from the User table for this user
     * @return mixed
     */
    function getUserDetails($userID){
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

    /**
     * Returns the details needed for display on the profile page given the listing ID
     *
     * @param $listingID
     * @return mixed
     */
    function getCardDetails($listingID){
        $statement = $this->db->prepare("
        SELECT `Listing`.`ListingID`, `Listing`.`Quantity`, `Listing`.`Time_Of_Creation`,
                `Item`.`ItemID`, `Item`.`Name`, `Item`.`Description`, 
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
        return $results[0];
    }

    /**
     * Returns the default image for this listing (if there is one)
     * @param $listingID
     * @return mixed
     */
    function getDefaultImage($listingID){
        $statement = $this->db->prepare("
            SELECT `Image`.`Image_URL` 
            FROM `Image`
            JOIN `ItemImage` ON `ItemImage`.`FK_Image_ImageID` = `Image`.`ImageID`
            JOIN `Item` ON `ItemImage`.`FK_Item_ItemID` = `Item`.`ItemID`
            JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            WHERE `Listing`.`ListingID` = :listingID
            AND `ItemImage`.`Is_Default` = 1
	    ORDER BY `Image`.`ImageID` DESC;
        ");
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results[0];
    }

    /**
    * Returns 1 if given user has an ongoing request for the given listing
    * @param $userID
    * @param $listingID
    * @return boolean
    */
    function isUserRequestingListing($userID, $listingID){
	$statement = $this->db->prepare("
		SELECT * 
		FROM `ListingTransaction`
		JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_TransactionID`
		JOIN `Listing` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
		JOIN `User` ON `User`.`UserID` = `Transaction`.`FK_User_UserID`
		WHERE `ListingTransaction`.`Success` = 0
		AND `Listing`.`ListingID` = :listingID
		AND `User`.`UserID` = :userID
	");
	    
	$statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
	$statement->bindValue(":userID", $userID, PDO::PARAM_INT);
	    
	$statement->execute();
	$results = $statement->fetchAll(PDO::FETCH_ASSOC);
	    
	return (count($results) > 0);    
    }
}
