<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 01/03/17
 * Time: 21:34
 */

namespace Wastetopia\Model;

use PDO;
use Wastetopia\Model\DB;
class Listing
{
    public function __construct()
    {
        $this->db = DB::getDB();
    }


    public function getDetails($listingID) {
        $statement = $this->db->prepare("
                        SELECT Quantity, Time_Of_Creation, Location.Name, Post_Code, Item.ItemDescription, Item.Use_By, User.Forename, User.Surname, User.Email_Address, User.Mean_Rating_Percent, User.Picture_URL
                        FROM Listing, Location, Item
                        WHERE Listing.FK_Location_LocationID = Location.LocationID
                          AND Listing.FK_Item_ItemID = Item.ItemID
                          AND Listing.FK_User_UserID = User.UserID
                          AND Listing.ListingID = :lID
                     ");
        $statement->bindValue(":lID", $listingID);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
}