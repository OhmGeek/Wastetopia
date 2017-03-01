<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 28/02/2017
 * Time: 11:43
 */
namespace Wastetopia\Model;
use PDO;


class SearchModel
{

    /**
     * SearchModel constructor.
     */
    public function __construct()
    {
        $this->db = DB::getDB();
    }


    /**
     * Returns all the listing IDs of items matching $name
     * @param $name
     * @return mixed
     */
    function getListingIDsFromName($name){
        $statement = $this->db->prepare("
            SELECT `Listing`.`ListingID`
            FROM `Listing`
            JOIN `Location` ON `Listing`.`FK_Location_LocationID` = `Location`.`LocationID`
            JOIN `Item` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            JOIN `ItemImage` ON `Item`.`ItemID` = `ItemImage`.`FK_Item_ItemID`
            JOIN `Image` ON `ItemImage`.`FK_Item_ItemID` = `Image`.`ImageID`
            WHERE `Item`.`Name` LIKE %:name%
            AND `Image`.`IsDefault` = 1;
        ");

        $statement->bindValue(":name", $name, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Searches by name, returns all general details needed for card display on search page
     * @param $name (String name user searches for)
     * @return array (array of details - ID, quantity, image url, item name, item description, post_code)
     */
    function getGeneralDetailsOfListingsByName($name){
        $statement = $this->db->prepare("
            SELECT `Listing`.`ListingID`, `Listing`.`Quantity`, `Image`.`Image_URL`, `Item`.`Name`, `Item`.`Description`, `Location`.`Post_Code`
            FROM `Listing`
            JOIN `Location` ON `Listing`.`FK_Location_LocationID` = `Location`.`LocationID`
            JOIN `Item` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            JOIN `ItemImage` ON `Item`.`ItemID` = `ItemImage`.`FK_Item_ItemID`
            JOIN `Image` ON `ItemImage`.`FK_Item_ItemID` = `Image`.`ImageID`
            WHERE `Item`.`Name` LIKE %:name%
            AND `Image`.`IsDefault` = 1;
        ");

        $statement->bindValue(":name", $name, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Returns the details needed for display on the search page given the listing ID
     * @param $listingID
     * @return mixed
     */
    function getCardDetails($listingID){
        $statement = $this->db->prepare("
            SELECT `Listing`.`ListingID`, `Listing`.`Quantity`, `Image`.`Image_URL`, `Item`.`Name`, `Item`.`Description`, `Location`.`Post_Code`
            FROM `Listing`
            JOIN `Location` ON `Listing`.`FK_Location_LocationID` = `Location`.`LocationID`
            JOIN `Item` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            JOIN `ItemImage` ON `Item`.`ItemID` = `ItemImage`.`FK_Item_ItemID`
            JOIN `Image` ON `ItemImage`.`FK_Item_ItemID` = `Image`.`ImageID`
            WHERE `Listing`.`ListingID` = :listingID
            AND `Image`.`IsDefault` = 1;
        ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    
}