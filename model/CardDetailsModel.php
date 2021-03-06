<?php
/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 04/04/2017
 * Time: 11:34
 */

namespace Wastetopia\Model;
use Wastetopia\Model\DB;
use Wastetopia\Model\UserCookieReader;
use Wastetopia\Config\CurrentConfig;
use PDO;

/**
 * Class CardDetailsModel - Used to get Display information for the Cards
 * @package Wastetopia\Model
 */
class CardDetailsModel
{


    /**
     * CardDetailsModel constructor.
     */
    public function __construct()
    {
        $this->db = DB::getDB();
	    
	// Get config details    
	$currentConfig = new CurrentConfig();
	$this->config = $currentConfig->getAll();
    }


    /**
     * Gets current user who's logged in
     * @return int
     */
    function getUserID(){
        $reader = new UserCookieReader();
         return $reader->get_user_id();
    }


    /**
     * Gets all details from the User table for the given user
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
     * Gets the profile picture of the given user
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
        $result = $statement->fetchColumn();
	    
	if(!$result){
		// No image, add default
		$result = $this->config["ROOT_IMG"]."/USR.png"; // Default image
	}
	    
	    return $result;
    }

    /**
     * Returns the details needed for display on the profile page given the listing ID
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
     * @return String - Image URL or empty string if no default image found
     */
    function getDefaultImage($listingID){
	    error_log("Getting default image");
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

	error_log("Image: ");
	error_log(json_encode($results));
	    
        if (count($results) == 0){
            return $this->config["ROOT_IMG"]."/PCI.png";
        }

	    $result = $results[0];
        return $result["Image_URL"];
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
    * Checks whether the given user has the listing in their watch list
    * @param $listingID
    * @param $userID
    * @return bool (True if user is requesting the listing)
    */	
    function isWatching($listingID, $userID){
	$statement = $this->db->prepare("
            SELECT COUNT(*) AS `Count`
	    FROM `Watch`
	    WHERE `FK_User_UserID` = :userID
	    AND `FK_Listing_ListingID` = :listingID;
        ");
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->execute();
	
        return $statement->fetchColumn() > 0;	
    }
}
