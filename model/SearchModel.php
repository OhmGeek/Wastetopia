<?php

include 'db.php';

class SearchModel
{

    function __construct()
    {

    }
    function getItemIDsFromSearch($searchTerm)
    {
        $db = DB::getDB();

        $fullSearchTerm = "%".$searchTerm."%";

        $statement = $db->prepare("SELECT Listing.ListingID AS listing_id
                                   FROM Listing, UserItem, Item
                                   WHERE Listing.FK_UserItem_UserItemID = UserItem.UserItemID
                                   AND UserItem.FK_Item_ItemID = Item.ItemID 
                                   AND Item.Name 
                                   LIKE :searchTerm ;");

        $statement->bindValue(':searchTerm', $fullSearchTerm, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function getItemDataFromID($itemID)
    {
        $db = DB::getDB();

        $statement = $db->prepare("SELECT (SELECT Image.Image_URL 
                                           FROM Image, UserItemImage,UserItem, Listing  
                                           WHERE Image.ImageID = UserItemImage.FK_Image_ImageID 
                                           AND UserItem.UserItemID = UserItemImage.FK_UserItem_UserItemID 
                                           AND UserItem.UserItemID = Listing.FK_UserItem_UserItemID 
                                           AND Listing.ListingID = :itemID
                                           AND UserItemImage.Is_Default = (SELECT MAX(UserItemImage.Is_Default) 
                                                                           FROM UserItemImage ) 
                                           GROUP BY UserItem.UserItemID) AS default_user_image_url, (SELECT Image.Image_URL 
                                                                                                     FROM Image, ItemImage, Item, UserItem, Listing  
                                                                                                     WHERE Image.ImageID = ItemImage.FK_Image_ImageID 
                                                                                                     AND Item.ItemID = ItemImage.FK_Item_ItemID 
                                                                                                     AND UserItem.FK_Item_ItemID = Item.ItemID 
                                                                                                     AND UserItem.UserItemID = Listing.FK_UserItem_UserItemID 
                                                                                                     AND Listing.ListingID = :itemID
                                                                                                     AND ItemImage.Is_Default = (SELECT MAX(ItemImage.Is_Default) 
                                                                                                                                 FROM ItemImage ) 
                                                                                                     GROUP BY UserItem.UserItemID) AS default_item_image_url,
                                                                                                     Item.Name AS item_name, 
                                                                                                     Item.Description AS manufacturer_description, 
                                                                                                     UserItem.Description AS user_description, 
                                                                                                     Listing.Time_Of_Creation AS date_added, 
                                                                                                     User.Forename AS forename, 
                                                                                                     User.Surname AS surname,
                                                                                                     User.Picture_URL AS profile_picture_url
                                                                                                     FROM Listing, User, UserItem, UserItemImage, Image, ItemImage, Item
                                                                                                     WHERE Listing.ListingID = :itemID
                                                                                                     AND User.UserID = Listing.FK_User_UserID 
                                                                                                     AND UserItem.UserItemID = Listing.FK_UserItem_UserItemID 
                                                                                                     AND Item.ItemID = UserItem.FK_Item_ItemID
                                                                                                     GROUP BY Listing.ListingID ;");

        $statement->bindValue(':itemID', $itemID, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}