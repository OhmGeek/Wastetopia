<?php

namespace Wastetopia\Model;
use PDO;


class AddItemModel
{


    function __construct()
    {
        $this->db = DB::getDB();
    }


    /**
     * Returns the ID of the user currently logged in
     * @return string
     */
    private function getUserID()
    {
          return 1; //for now, return 1 for testing.
//        $reader = new UserCookieReader();
//        return $reader->get_user_id();
    }


    /**
     * Returns the ID of the last thing inserted in the database
     * @return int
     */
    private function getLastInsertID()
    {
        $statement = $this->db->prepare("
            SELECT LAST_INSERT_ID()
         ");
        $statement->execute();
        return $statement->fetchColumn();
    }

    /**
     * Returns the ID of the last item inserted with the given parameters
     * @param $name
     * @param $useBy
     * @param $description
     * @return int
     */
    private function getLastItemID($name, $useBy, $description)
    {
        $statement = $this->db->prepare("
            SELECT `Item`.`ItemID`
	      FROM `Item`
	      WHERE `Item`.`Name` = :name
	      AND `Item`.`Description` = :description
	      ORDER BY `Item`.`ItemID` DESC;
         ");

	$statement->bindValue(":name", $name, PDO::PARAM_STR);
	$statement->bindValue(":description", $description, PDO::PARAM_STR);
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC)[0];
    error_log("Get Last ItemID:");
    error_log($results["ItemID"]);
	return $results["ItemID"];
    }


    /**
     * Returns the ID of the last image inserted with the given parameters
     * @param $fileType
     * @param $imageURL
     * @return int
     */
    function getLastImageID($fileType, $imageURL){
        $statement = $this->db->prepare("
            SELECT Image.ImageID
            FROM Image
            WHERE File_Type = :fileType
            AND Image_URL = :imageURL
         ");

        $statement->bindValue(":fileType", $fileType, PDO::PARAM_STR);
        $statement->bindValue(":imageURL", $imageURL, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
    }

    /**
     * Returns the ID of the last location inserted with the given parameters
     * @param $name
     * @param $postCode
     * @param $long
     * @param $lat
     * @return int
     */
    function getLastLocationID($name, $postCode, $long, $lat){
        $statement = $this->db->prepare("
            SELECT Location.LocationID
            FROM Location
            WHERE Name = :name
            AND Post_Code = :postCode
            AND Longitude = :long
            AND Latitude = :lat
            ORDER BY LocationID DESC;
         ");
        error_log("Name");
        error_log($name);
        $statement->bindValue(":name", $name, PDO::PARAM_STR);
        $statement->bindValue(":postCode", $postCode, PDO::PARAM_STR);
        $statement->bindValue(":long", $long, PDO::PARAM_STR);
        $statement->bindValue(":lat", $lat, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC)[0];
        error_log(json_encode($statement->errorInfo()));
	    return $results["LocationID"];
    }


    /**
     * Gets all the select options user can choose for tags
     * @param $categoryID (category to search for)
     * @return mixed
     */
    function getAllTagOptions($categoryID){
        $statement = $this->db->prepare("
            SELECT `Tag`.`Name`
            FROM `Tag`
            WHERE `Tag`.`FK_Category_Category_ID` = :catID
            ORDER BY `Tag`.`Name`
         ");

        $statement->bindValue(':catID',$categoryID,PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_COLUMN);

    }



    /*QUERIES TO INSERT INTO INDIVIDUAL TABLES*/


    /**
     * Inserts Item data into the Item table
     * @param $name (string name)
     * @param $description (string description)
     * @param $useByDate (in the form YYYY-MM-DD)
     * @return int (The ID of the inserted Item)
     */
    function addToItemTable($name, $description, $useByDate)
    {
        $statement = $this->db->prepare("
            INSERT INTO Item (Name, Description, Use_By)
            VALUES ( :name, :description, STR_TO_DATE(:useByDate, '%e %M, %Y'));
         ");
        error_log("Name:");
        error_log($name);

        $statement->bindValue(":name", $name, PDO::PARAM_STR);
        $statement->bindValue(":description", $description, PDO::PARAM_STR);
        $statement->bindValue(":useByDate", $useByDate, PDO::PARAM_STR);

        $statement->execute();
        error_log(json_encode($statement->errorInfo()));
        $lastItemID = $this->getLastItemID($name, $useByDate, $description);
        error_log($lastItemID);
        return $lastItemID;
    }

    /**
     * Links a tag to an item
     * @param $itemID
     * @param $tagID
     */
    function addToItemTagTable($itemID, $tagID)
    {
        $statement = $this->db->prepare("
            INSERT INTO `ItemTag` (`FK_Item_ItemID`, `FK_Tag_TagID`)
            VALUES (:itemID, :tagID);
         ");

        $statement->bindValue(":itemID", $itemID, PDO::PARAM_INT);
        $statement->bindValue(":tagID", $tagID, PDO::PARAM_INT);
        $statement->execute();
    }


    /**
     * Adds to image table
     * @param $fileType (string file-type, e.g JPG)
     * @param $imageURL (string URL to location of image in website)
     * @return int (the ID of the image inserted)
     */
    function addToImageTable($fileType, $imageURL)
    {
        $statement = $this->db->prepare("
            INSERT INTO `Image` (`File_Type`, `Image_URL`)
            VALUES (:fileType, :imageURL);
         ");

        $statement->bindValue(":fileType", $fileType, PDO::PARAM_STR);
        $statement->bindValue(":imageURL", $imageURL, PDO::PARAM_STR);
        $statement->execute();

        //return $this->getLastInsertID(); // Need to change to another sql query
        return $this->getLastImageID($fileType, $imageURL);
    }




    /**
     * Links an image to an item
     * @param $imageID
     * @param $itemID
     * @param $isDefault (1 if this image is the main image for the item)
     */
    function addToItemImageTable($imageID, $itemID, $isDefault)
    {
        $statement = $this->db->prepare("
            INSERT INTO `ItemImage` (`FK_Item_ItemID`, `Is_Default`, `FK_Image_ImageID`)
            VALUES (:itemID, :isDefault, :imageID);
         ");

        $statement->bindValue(":itemID", $itemID, PDO::PARAM_INT);
        $statement->bindValue(":isDefault", $isDefault, PDO::PARAM_INT);
        $statement->bindValue(":imageID", $imageID, PDO::PARAM_INT);
        $statement->execute();
    }


    /**
     * Adds a barcode and links it to the item
     * @param $itemID
     * @param $barcode (Integer barcode)
     * @param $barcodeType (String representation of its type)
     */
    function addToBarcodeTable($itemID, $barcode, $barcodeType)
    {
        $statement = $this->db->prepare("
            INSERT INTO `Barcode` (`Barcode`, `Barcode_Type`, `FK_Item_ItemID`)
            VALUES (:barcode, :barcodeType, :itemID);
         ");

        $statement->bindValue(":barcode", $barcode, PDO::PARAM_INT);
        $statement->bindValue(":barcodeType", $barcodeType, PDO::PARAM_STR);
        $statement->bindValue(":itemID", $itemID, PDO::PARAM_INT);
        $statement->execute();
    }


    /**
     * Adds the location of the listing to the location table
     * @param $name (String name of location, e.g My house)
     * @param $postCode (String format)
     * @return int (the ID inserted into the location table)
     */
    function addToLocationTable($name, $postCode, $long, $lat)
    {
        $statement = $this->db->prepare("
            INSERT INTO `Location` (`Name`, `Post_Code`, `Longitude`, `Latitude`)
            VALUES (:name, :postCode, :long, :lat);
         ");

        $statement->bindValue(":name", $name, PDO::PARAM_STR);
        $statement->bindValue(":postCode", $postCode, PDO::PARAM_STR);
        $statement->bindValue(":long", $long, PDO::PARAM_STR);
        $statement->bindValue(":lat", $lat, PDO::PARAM_STR);
        $statement->execute();
        return $this->getLastLocationID($name,$postCode,$long, $lat); // Changed from getLastInsertID()
    }




    /**
     * Adds the details to the listing table
     * @param $locationID
     * @param $itemID
     * @param $quantity
     */
    function addToListingTable($locationID, $itemID, $quantity)
    {
        $userID = $this->getUserID();

        $statement = $this->db->prepare("
            INSERT INTO `Listing` (`FK_Location_LocationID`, `FK_Item_ItemID`, `FK_User_UserID`, `Quantity`, `Time_Of_Creation`, `ACTIVE`)
            VALUES (:locationID, :itemID, :userID, :quantity, NOW(), 1);
         ");

        $statement->bindValue(":locationID", $locationID, PDO::PARAM_INT);
        $statement->bindValue(":itemID", $itemID, PDO::PARAM_INT);
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->bindValue(":quantity", $quantity, PDO::PARAM_INT);
        $statement->execute();
        error_log(json_encode($statement->errorInfo()));
    }



    /*MAIN FUNCTIONS LINKING ABOVE FUNCTIONS TOGETHER*/


    /**
     * Calls functions to add all the tags and then link the tags to the item
     * @param $itemID
     * @param $tags (Array of tag arrays in the form ["tagID"=>tagID])
     */
    function addAllTags($itemID, $tags)
    {

        foreach ($tags as $tag) {
//            $tagName = $tag["name"];
//            $tagCategoryId = $tag["categoryID"];
//            $tagDescription = $tag["description"];
            //$tagID = $this->addToTagTable($tagName, $tagCategoryId, $tagDescription); //Add the tag
            $tagID = $tag["tagID"];
            $this->addToItemTagTable($itemID, $tagID); //Link tag to item
        }
    }


    /**
     * Adds all the images to the database and links them to the item
     * @param $itemID
     * @param $images (Array of image arrays in the form ["fileType"=>fileType, "url"=>url, "isDefault"=>isDefault])
     */
    function addAllImages($itemID, $images)
    {
        $isDefault = 1;
        foreach ($images as $image) {
            $imageURL = $image["url"];
            $imageID = $this->getImageIDFromURL($imageURL); //Add to image table
            $this->addToItemImageTable($imageID, $isDefault, $itemID); //Link image to item
            $isDefault = 0; // first image is default, others aren't.
        }
    }


    /**
     * Calls all the other linking functions and is the only one needed by the user
     * @param $item (Associative array in the form ["itemName"=>name, "itemDescription"=>description, "useByDate"=>date, "quantity"=>quantity])
     * @param $tags (Array of tag arrays in the form ["tagID"=>tagID]) (Seems like that's what it's using)
     * @param $images (Array of image arrays in the form ["fileType"=>fileType, "url"=>url, "isDefault"=>isDefault])
     * @param $barcode (Associative array in the form ["barcodeNumber"=>number, "barcodeType"=>type])
     * @param $location (Associative array in the form ["locationName"=>name, "postCode"=>postCode])
     */
    function mainAddItemFunction($item, $tags, $images, $barcode, $location)
    {
        //Extract item information
        $itemName = $item["itemName"];
        error_log(json_encode($item));
        $itemDescription = $item["itemDescription"];
        $useByDate = $item["useByDate"];
        if(!isset($useByDate)) {
            $useByDate = "1st January, 1970";
        }
        $itemID = $this->addToItemTable($itemName, $itemDescription, $useByDate); //Add the item
        error_log("Item ID:");
        error_log($itemID);
        if(isset($tags) && count($tags) > 0) {
            error_log("Add all tags");
            $this->addAllTags($itemID, $tags); //Add the tags and link to item
        }

        if(isset($images) && count($images) > 0) {
            $this->addAllImages($itemID, $images); //Add the images and link to item
        }
        //Extract location information
        $locationName = $location["firstLineAddr"];
        $postCode = $location["secondLineAddr"];

        $locationID = $this->addToLocationTable($locationName, $postCode, $location['long'], $location['lat']); //Add the location to the database

        if(isset($barcode)) {
            //Extract barcode information
            $barcodeNumber = $barcode["barcodeNumber"];
            $barcodeType = $barcode["barcodeType"];
            $this->addToBarcodeTable($itemID, $barcodeNumber, $barcodeType); //Add barcode and link to item
        }
        //Add the whole listing                       quantity=1
        $this->addToListingTable($locationID, $itemID, 1);
    }

    private function getImageIDFromURL($imageURL)
    {
        $statement = $this->db->prepare("
                            SELECT ImageID
                            FROM Image
                            WHERE Image_URL = :url");

        $statement->bindValue(":url", $imageURL,PDO::PARAM_STR);
        $statement->execute();
        // return the ID, or nothing if none is found.
        return $statement->fetchColumn(0);
    }

    public function getTagDetails($name)
    {
        $statement = $this->db->prepare("
                            SELECT *
                            FROM Tag
                            WHERE Tag.Name = :name
                            ");

        $statement->bindValue(":name", $name,PDO::PARAM_STR);
        $statement->execute();
        // return the ID, or nothing if none is found.
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        // now we have the results, create a tag and return it.
        error_log(json_encode($results));
        return array(
            'name' => $results[0]['Name'],
            'categoryID' => $results[0]['FK_Category_Category_ID'],
            'description' => $results[0]['Description']
        );
    }
}
