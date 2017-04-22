<?php

//BASICALLY UNTESTED

namespace Wastetopia\Model;
use PDO;
use Wastetopia\Model\DB;


class ItemModel
{
    /**
     * ItemModel constructor.
     */
    function __construct()
	{
		$this->db = DB::getDB();
	}

    /**
	 * Get Item Info from ItemID
     * @param $itemID
     * @return array
     */
    function getItemInfoFromItemID($itemID){
		
		$statement = $this->db->prepare("
			SELECT *
			FROM Item
			WHERE ItemID = :item_id;
		");
		
		$statement->bindValue(":item_id", $itemID, PDO::PARAM_INT);
		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

    /**
	 * Get item info from the current listing
     * @param $listingID
     * @return array
     */
    function getItemInfoFromListingID($listingID){
		$statement = $this->db->prepare("
			SELECT * 
			FROM Item
			RIGHT JOIN Listing ON Item.ItemID = Listing.FK_Item_ItemID
			WHERE Listing.ListingID = :listing_id;
		");
		$statement->bindValue(":listing_id", $listingID, PDO::PARAM_INT);
		$statement->execute();
		
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}



