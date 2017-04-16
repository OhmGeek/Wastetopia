<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 28/02/2017
 * Time: 11:27
 */
namespace Wastetopia\Model;

use PDO;
use Wastetopia\Model\AddItemModel;
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
     * @param $listingID (The ListingID to get item details for)
     * @return array (an array containing name, description, and expiry date)
     */
    function getItemDetails($listingID) {
        $statement = $this->db->prepare("
            SELECT Description, Use_By, Name
            FROM Item, Listing
            WHERE Listing.ListingID = :listingID
              AND Listing.FK_Item_ItemID = Item.ItemID
        ");
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        // now return an array with the correct information.
        if(count($results) == 0) {
            return array();
        }
        return array(
            "name" => $results[0]["Name"],
            "description" => $results[0]["Description"],
            "expires" => $results[0]["Use_By"]
        );
    }

    function getItemStatus($listingID) {
        $statement = $this->db->prepare("
            SELECT Active
            FROM Listing
            WHERE ListingID = :listingID 
        ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return array(
            "active" => $results[0]["Active"]
        );
    }

    function getTagDetails($listingID) {
            $statement = $this->db->prepare("
            SELECT Tag.TagID, Tag.Name, Tag.FK_Category_Category_ID, Tag.Description
            FROM Tag, ItemTag, Listing
            WHERE ItemTag.FK_Item_ItemID = Listing.FK_Item_ItemID
              AND ItemTag.FK_Tag_TagID = Tag.TagID
              AND Listing.ListingID = :listingID
            ORDER BY `Tag`.`Name`
         ");

            $statement->bindValue(':listingID',$listingID,PDO::PARAM_INT);
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            // here are the array fields to insert into based on Category
            $field = array(
                1 => "type",
                2 => "state",
                3 => "dietary",
                4 => "contains",
                5 => "other"
            );
            // this is the data we return
            $data = array(
                "type" => array(),
                "state" => array(),
                "dietary" => array(),
                "contains" => array(),
                "other" => array()

            );
            //process results
            foreach($results as $tag) {
                array_push($data[$field[$tag['FK_Category_Category_ID']]], array(
                    "name" => $tag['Name'],
                    "description" => $tag['Description']
                ));
            }
            error_log(json_encode($results));
            error_log(json_encode($data));
            return $data;

    }

    function getImages($listingID) {
        $statement = $this->db->prepare("
            SELECT Image.ImageID, Image_URL
            FROM Image, Listing, ItemImage
            WHERE Image.ImageID = ItemImage.FK_Image_ImageID
              AND ItemImage.FK_Item_ItemID = Listing.FK_Item_ItemID
              AND Listing.ListingID = :listingID
        ");

        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $imageOutput = array();
        // we need to process results
        foreach($results as $image) {
            array_push($imageOutput, (object) array(
                "id" => $image["ImageID"],
                "url" => $image["Image_URL"]
            ));
        }
        error_log(json_encode($imageOutput));
        // return images
        return array(
            "images" => $imageOutput
        );
    }

    function getLocation($listingID) {
        $statement = $this->db->prepare("
            SELECT Name, Post_Code, Longitude, Latitude, Country
            FROM Location, Listing
            WHERE Location.LocationID = Listing.FK_Location_LocationID
              AND Listing.ListingID = :listingID
        ");
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);

        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        if(!isset($results) || count($results) <= 0) {
            return array();
        }
        return array(
            "location" => array(
                "long" => $results[0]["Longitude"],
                "lat" => $results[0]["Latitude"],
                "postcode" => $results[0]["Post_Code"],
                "name" => $results[0]["Name"]
            )
        );
    }

    function getBarcode($listingID) {
        $statement = $this->db->prepare("
            SELECT *
            FROM Barcode, Listing
            WHERE Listing.FK_Item_ItemID = Barcode.FK_Item_ItemID
              AND Listing.ListingID = :listingID
        ");
        $statement->bindValue(":listingID",$listingID, PDO::PARAM_INT);
        $statement->execute();
        error_log(json_encode($statement->errorInfo()));
        $barcodeDBResults = $statement->fetchAll(PDO::FETCH_ASSOC);
        $barcode = array("barcode" => "");
        if(count($barcodeDBResults) > 0) {
                error_log("This includes barcode");
                $barcode["barcode"] = $barcodeDBResults[0]["Barcode"];
        };
        error_log(json_encode($barcodeDBResults));
        return $barcode;
    }


    /**
     * Returns all details, images and tags relating to a given listing
     * @param $listingID
     * @return array
     */
    function getAll($listingID){
        $itemSerialised = array();
        $itemSerialised = array_merge($itemSerialised, $this->getTagDetails($listingID));
        $itemSerialised = array_merge($itemSerialised, $this->getItemDetails($listingID));
        $itemSerialised = array_merge($itemSerialised, $this->getItemStatus($listingID));
        $itemSerialised = array_merge($itemSerialised, $this->getImages($listingID));
        $itemSerialised = array_merge($itemSerialised, $this->getBarcode($listingID));
        $itemSerialised = array_merge($itemSerialised, $this->getLocation($listingID));

        error_log(json_encode($itemSerialised));
        return $itemSerialised;
    }

}

