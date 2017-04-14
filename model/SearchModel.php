<?php
/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 28/02/2017
 * Time: 11:43
 */
/**
 * All the search queries will return arrays of listingIDs that match that search.
 * You must then call the function getCardDetails($listingID) with each of those IDs to get the details for display.
 * This allows the card display details to be changed in one function.
 */
namespace Wastetopia\Model;

use Wastetopia\Model\DB;
use \PDO;

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
     * Returns the details needed for display on the search page given the listing ID
     *
     * @param $listingID
     * @return mixed
     */
    function getCardDetails($listingID){
        $statement = $this->db->prepare("
        SELECT `Listing`.`ListingID`, `Listing`.`Quantity`, `Listing`.`Time_Of_Creation`,
                `Item`.`ItemID`, `Item`.`Name`, `Item`.`Description`, 
                `Location`.`Post_Code`, `Location`.`Latitude`, `Location`.`Longitude`,
                `User`.`UserID`, `User`.`Forename`, `User`.`Surname`, `User`.`Picture_URL`
        FROM `Listing`
        JOIN `User` ON `Listing`.`FK_User_UserID` = `User`.`UserID`
        JOIN `Location` ON `Listing`.`FK_Location_LocationID` = `Location`.`LocationID`
        JOIN `Item` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
        WHERE `Listing`.`ListingID` = :listingID;
        ");
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC)[0];
    }
    
    function getDefaultImage($listingID)
    {
        $statement = $this->db->prepare("
            SELECT `Image`.`Image_URL` 
            FROM `Image`
            JOIN `ItemImage` ON `ItemImage`.`FK_Image_ImageID` = `Image`.`ImageID`
            JOIN `Item` ON `ItemImage`.`FK_Item_ItemID` = `Item`.`ItemID`
            JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            WHERE `Listing`.`ListingID` = :listingID
            
        ORDER BY `Image`.`ImageID` DESC;
        "); //AND `ItemImage`.`Is_Default` = 1
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->execute(); 
        return $statement->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    function checkRequestingStatus($listingID, $userID)
    {
        $statement = $this->db->prepare("

        ");
    }

    /*Distance searches will return listings only within 0.76 degrees of search location
      This draws a square of hight 77km and of variable width based on the latitude
      and returns results only within this area
      At the equator the width is roughly 77km, at London, 56km
      and at the UK Arctic Research Station, 30km*/
    function getSearchResults($userLat, $userLong, $search, $tagsArray, $distanceLimit = 0.76)
    {

        $sql = "SELECT `Listing`.`ListingID`, `Location`.`Latitude`, `Location`.`Longitude`
            FROM `Listing`
            JOIN `Item` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            JOIN `Location` ON `Listing`.`FK_Location_LocationID` = `Location`.`LocationID`
            WHERE `Listing`.`Quantity` > 0
            ";
        if (($userLat !== false) && ($userLong !== false))
        {
            $sql .= "AND ABS(`Location`.`Latitude` - :userLat) < :distanceLimit
                     AND ABS(`Location`.`Longitude` - :userLong) < :distanceLimit
                     ";

        }
        if ($search !== false)
        {
            $sql .= "AND `Item`.`Name` LIKE :search
            ";
        }
        if ($tagsArray !== false)
        {
            $tagCount = count($tagsArray);

            $sql .= "AND `Listing`.`ListingID` IN (SELECT `TagCount`.`ListingID`
                                 FROM (SELECT `Listing`.`ListingID`, COUNT(DISTINCT `ItemTag`.`FK_Tag_TagID`) AS `Count`
                                       FROM `Listing`
                                       JOIN `Item` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
                                       JOIN `ItemTag` ON `Item`.`ItemID` = `ItemTag`.`FK_Item_ItemID`
                                       WHERE `ItemTag`.`FK_Tag_TagID` IN (";

            foreach ($tagsArray as $key => $tag) 
            {
                if ($key == ($tagCount-1))
                {
                    $sql .= ":tag".$key;
                }
                else
                {
                    $sql .= ":tag".$key.",";
                }
                
            }

            $sql .= ")
                     GROUP BY `Listing`.`ListingID`
                         ) as `TagCount`
                     WHERE `TagCount`.`Count` = :tagCount)";
        }                

        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $statement = $this->db->prepare($sql);
        if ($tagsArray !== false)
        {
            foreach ($tagsArray as $key => $tag)
            {
                $statement->bindValue(":tag".$key, $tag, PDO::PARAM_INT);
            }
            $statement->bindValue(":tagCount", strval($tagCount), PDO::PARAM_STR);
        }
        if (($userLat !== false) && ($userLong !== false))
        {
            $statement->bindValue(":userLat", strval($userLat), PDO::PARAM_STR);
            $statement->bindValue(":userLong", strval($userLong), PDO::PARAM_STR);
            $statement->bindValue(":distanceLimit", strval($distanceLimit), PDO::PARAM_STR);
        } 
        if ($search !== false)
        {
            $statement->bindValue(":search", strval('%'.$search.'%'), PDO::PARAM_STR);
        }
        
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getReccomendationResults($tagsArray, $currentUserID)
    {

        $tagCount = count($tagsArray);

        $sql = "SELECT `TagCount`.`ListingID`
                FROM (
                    SELECT `Listing`.`ListingID`, COUNT(DISTINCT `ItemTag`.`FK_Tag_TagID`) AS `Count`
                    FROM `Listing`
                    JOIN `Item` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
                    JOIN `ItemTag` ON `Item`.`ItemID` = `ItemTag`.`FK_Item_ItemID`
                    WHERE `ItemTag`.`FK_Tag_TagID` IN (";

        foreach ($tagsArray as $key => $tag) 
        {
            if ($key == ($tagCount-1))
            {
                $sql .= ":tag".$key;
            }
            else
            {
                $sql .= ":tag".$key.",";
            }
        }

        $sql .=    ")
                    AND NOT(`Listing`.`FK_User_UserID` = :currentUser)
                    AND `Listing`.`Active` = 1
                    AND `Listing`.`Quantity` > 0
                    GROUP BY `Listing`.`ListingID`
                    ) as `TagCount`
                ORDER BY `TagCount`.`Count` DESC;";

        $statement = $this->db->prepare($sql);

        foreach ($tagsArray as $key => $tag)
        {
            $statement->bindValue(":tag".$key, $tag, PDO::PARAM_INT);
        }
        $statement->bindValue(":currentUser", $currentUserID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


















    /*LOCATION FUNCTIONS USING GOOGLE API*/

    /**
     * Searches for exact post code matches
     * @param $postCode
     * @return mixed (array of listingIDs)
     */
    function getListingIDsFromPostCode($postCode){
        $statement = $this->db->prepare("
            SELECT `Listing`.`ListingID`
            FROM `Listing`
            JOIN `Location` ON `Listing`.`FK_Location_LocationID` = `Location`.`LocationID`
            WHERE `Location`.`Post_Code` = ':postCode';
        ");
        $statement->bindValue(":postCode", $postCode, PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //FUNCTIONS TO DEAL WITH DISTANCE, MAY BE IN CONTROLLER
    //NEED TESTING
    /**
     * Gets the post code of the listing (Not necessary if you've already searched as the information is retrieved in those functions)
     * @param $listingID
     * @return mixed
     */
    function getPostCodeFromListing($listingID){
        $statement = $this->db->prepare("
            SELECT `Location`.`Post_Code`
            FROM `Listing`
            JOIN `Location` ON `Listing`.`FK_Location_LocationID` = `Location`.`LocationID`
            WHERE `Listing`.`ListingID` = :listingID;
        ");
        $statement->bindValue(":listingID", $listingID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchColumn();
    }
    /**
     * Uses Google API to convert a post code to Latitude and longitude
     * @param $postCode
     * @return array (in form ["latitude"=>latitude, "longitude"=>longitude])
     */
    function getLatLongFromPostCode($postCode){
        $url = "https://maps.googleapis.com/maps/api/geocode/json?components=postal_code:POSTCODE&key=YOUR_API_KEY";
        $result = file_get_contents($url); //Get JSON from google maps api
        $result = json_decode($result); //Decode JSON
        $status = $result["status"]; //Extract status
        if ($status === "OK"){ //Check results are there
            $results = $result["results"]; //Extract results
            $geometry = $results["geometry"];
            $location = $geometry["location"];
            $latitude = $location["lat"];
            $longitude = $location["lng"];
            return array("latitude"=>$latitude, "longitude"=>$longitude);
        }
    }
    /**
     * Uses Google API to convert a latitude-longitude pair into a post_code
     * @param $latitude
     * @param $longitude
     * @return array (in the form ["address"=>address])
     */
    function getPostCodeFromLatLong($latitude, $longitude){
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=LATITUDE,LONGITUDE&result_type=postal_code&key=YOUR_API_KEY";
        $result = file_get_contents($url); //Get JSON from google maps api
        $result = json_decode($result); //Decode JSON
        $status = $result["status"]; //Extract status
        if ($status === "OK"){ //Check results are there
            $results = $result["results"]; //Extract results
            $address = $results["formatted_address"];
            return array("address"=>$address);
        }
    }
    /**
     * Uses Google Distance Matrix API to get distance between two post codes
     * @param $userPostCode
     * @param $listingPostCode
     * @return int
     */
    function getDistanceBetweenUserAndListing($userPostCode, $listingPostCode){
        $userLocation = $this->getLatLongFromPostCode($userPostCode);
        $listingLocation = $this->getLatLongFromPostCode($listingPostCode);
        $userLatitude = $userLocation["latitude"];
        $userLongitude = $userLocation["longitude"];
        $listingLatitude = $listingLocation["latitude"];
        $listingLongitude = $listingLocation["longitude"];
        //Create the origins params for Google Distance matrix API
        $origins = $userLongitude.",".$userLatitude;
        $destinations = $listingLongitude.",".$listingLatitude;
        $key = "API_KEY_HERE";
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$origins."&destinations=".$destinations."&key=YOUR_API_KEY";
        $distance = $url["rows"];
        $distance = $distance["elements"];
        $distance = $distance["distance"];
        $distance = $distance["value"];
        return $distance;
    }
}
