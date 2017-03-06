<<<<<<< HEAD:model/ViewItemModel.php
<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 28/02/2017
 * Time: 11:27
 */
namespace Wastetopia\Model;

use PDO;

class ViewItemModel
{

    /**
     * ViewItemModel constructor.
     */
    public function __construct()
    {
        $this->db = DB::getDB();
    }


    /**
     * Returns all details about images associated with the item in this listing
     * @param $listingID
     * @return mixed
     */
    function getImages($listingID){
        $statement = $this->db->prepare("
            SELECT *
            FROM `Image` 
            JOIN `ItemImage` ON `ItemImage`.`FK_Image_ImageID` = `Image`.`ImageID`
            JOIN `Item` ON `ItemImage`.`FK_Item_ItemID` = `Item`.`ItemID`
            JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            WHERE `Listing`.`ListingID` = :listingID;
        ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Returns all details about tags associated with the item in this listing
     * @param $listingID
     * @return mixed
     */
    function getTags($listingID){
        $statement = $this->db->prepare("
            SELECT `Tag`.`Name`, `Tag`.`Description`, `Tag`.`FK_Category_Category_ID` as CategoryID
            FROM `Tag` 
            JOIN `ItemTag` ON `ItemTag`.`FK_Tag_TagID` = `Tag`.`TagID`
            JOIN `Item` ON `Item`.`ItemID` = `ItemTag`.`FK_Item_ItemID`
            JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            WHERE `Listing`.`ListingID` = :listingID;
        ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Returns all general details about an item (location, item details and listing details)
     * @param $listingID
     * @return mixed
     */
    function getDetails($listingID){
        $statement = $this->db->prepare("
            SELECT * 
            FROM `Listing`
            JOIN `Location` ON `Listing`.`FK_Location_LocationID` = `Location`.`LocationID`
            JOIN `Item` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            JOIN `Barcode` ON `Barcode`.`FK_Item_ItemID` = `Item`.`ItemID`
            WHERE `Listing`.`ListingID` = :listingID;
        ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    
    /**
    * Returns the barcode of an item (if it exists)
    * @param $listingID
    * @return int (barcode)
    */
    function getBarcode($listingID){
        $statement = $this->db->prepare("
            SELECT * 
            FROM `Barcode`
            JOIN `Item` ON `Item`.`ItemID` = `Barcode`.`FK_Item_ItemID`
            JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            WHERE `Listing`.`ListingID` = :listingID;
        ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchColumn();
    }
    
    
    /**
     * Returns all details, images and tags relating to a given listing
     * @param $listingID
     * @return array (in the form (["images"]=>images, ["tags"]=>tags, ["details"]=>generalDetails)
     */
    function getAll($listingID){
        $images = $this->getImages($listingID);
        $tags = $this->getTags($listingID);
        $generalDetails = $this->getDetails($listingID);
        $barcode = $this->getBarcode($listingID);
        
        return (array("images"=>$images,
                        "tags"=>$tags,
                        "details"=>$generalDetails,
                        "barcode"=>$barcode));
    }


}

