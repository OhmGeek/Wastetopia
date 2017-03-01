<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 27/02/2017
 * Time: 17:06
 */
class EditItemModel
{
    function __construct()
    {
        $this->db = DB::getDB();
    }

    /**
     * Changes the name of the Item in the given listing
     * @param $listingID
     * @param $name (New name)
     */
    function editItemName($listingID, $name){
        $statement = $this->db->prepare("
            UPDATE `Item` 
            JOIN `Listing` ON `ItemID` = `Listing`.`FK_Item_ItemID`
            SET `Name` = :name;
            WHERE ListingID = :listingID;
         ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->bindValue(":name", $name, PDO::PARAM_STR);
        $statement->execute();
    }

    /**
     * Changes the description of the Item in the given listing
     * @param $listingID
     * @param $description
     */
    function editItemDescription($listingID, $description){
        $statement = $this->db->prepare("
            UPDATE `Item`
            JOIN `Listing` ON `ItemID` = `Listing`.`FK_Item_ItemID`
            SET `Description` = :description
            WHERE ListingID = :listingID;
            
         ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->bindValue(":description", $description, PDO::PARAM_STR);
        $statement->execute();
    }

    /**
     * Changes the Use-by-date of the Item in the given listing
     * @param $listingID
     * @param $useByDate
     */
    function editItemUseByDate($listingID, $useByDate){
        $statement = $this->db->prepare("
            UPDATE `Item`
            JOIN `Listing` ON `ItemID` = `Listing`.`FK_Item_ItemID`
            SET `Use_By` = :useByDate
            WHERE ListingID = :listingID;
            
         ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->bindValue("useByDate", $useByDate, PDO::PARAM_STR);
        $statement->execute();
    }

    /**
     * Changes the quantity of the Items in the given listing
     * @param $listingID
     * @param $quantity
     */
    function editItemQuantity($listingID, $quantity){
        $statement = $this->db->prepare("
            UPDATE `Listing`
            SET `Quantity` = :quantity
            WHERE`ListingID` = :listingID;
            
         ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->bindValue(":quantity", $quantity, PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Changes the bacode number of the item in the given listing
     * @param $listingID
     * @param $barcodeNumber
     */
    function editItemBarcodeNumber($listingID, $barcodeNumber){
        $statement = $this->db->prepare("
            UPDATE `Barcode`
            JOIN `Item` ON `ItemID` = `Barcode`.`FK_Item_ItemID`
            JOIN `Listing` ON `ItemID` = `Listing`.`FK_Item_ItemID`
            SET `Barcode`.`Barcode` = :barcodeNumber
            WHERE`ListingID` = :listingID;
         ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->bindValue(":barcodeNumber", $barcodeNumber, PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Changes the barcode type of the item in the given listing
     * @param $listingID
     * @param $barcodeType
     */
    function editItemBarcodeType($listingID, $barcodeType){
        $statement = $this->db->prepare("
            UPDATE `Barcode`
            JOIN `Item` ON `ItemID` = `Barcode`.`FK_Item_ItemID`
            JOIN `Listing` ON `ItemID` = `Listing`.`FK_Item_ItemID`
            SET `Barcode_Type` = :barcodeType
            WHERE`ListingID` = :listingID;
         ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->bindValue(":barcodeType", $barcodeType, PDO::PARAM_STR);
        $statement->execute();
    }

    /**
     * Changes the location name of the given listing
     * @param $listingID
     * @param $locationName
     */
    function editLocationName($listingID, $locationName){
        $statement = $this->db->prepare("
            UPDATE `Location`
            JOIN `Listing` ON `LocationID` = `Listing`.`FK_Location_LocationID`
            SET `Location`.`Name` = :locationName
            WHERE`ListingID` = :listingID;
         ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->bindValue(":locationName", $locationName, PDO::PARAM_STR);
        $statement->execute();
    }

    /**
     * Changes the post code location of the given listing
     * @param $listingID
     * @param $postCode (String format)
     */
    function editLocationPostCode($listingID, $postCode){
        $statement = $this->db->prepare("
            UPDATE `Location`
            JOIN `Listing` ON `LocationID` = `Listing`.`FK_Location_LocationID`
            SET `Location`.`Post_Code` = :postCode
            WHERE`ListingID` = :listingID;
         ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->bindValue(":postCode", $postCode, PDO::PARAM_STR);
        $statement->execute();
    }

    //UNSURE WHAT THESE FUNCTIONS WILL DO (IF ANYTHING)
//
//    /**
//     * Changes the name and description of a given tag
//     * @param $listingID
//     * @param $categoryID
//     * @param $categoryName
//     * @param $description
//     */
//    function editTags($listingID, $categoryID, $categoryName, $description){
//
//    }
//
//    //Not needed as will just delete images and upload new ones?
//    /**
//     * Currently does nothing
//     */
//    function editImages(){
//
//    }



}