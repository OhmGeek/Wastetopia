<?php

//BASICALLY UNTESTED

namespace Wastetopia\Model;
use PDO;
use Wastetopia\Model\DB;


class ItemModel
{
	function __construct()
	{
		$this->db = DB::getDB();
	}

	function getItemInfoFromItemID($item_id){
		
		$statement = $this->db->prepare("
			SELECT *
			FROM Item
			WHERE ItemID = :item_id;
		");
		
		$statement->bindValue(":item_id", $item_id, PDO::PARAM_INT);
		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function getItemInfoFromListingID($listing_id){
		$statement0 = $this->db->prepare("
			SELECT ItemID 
			FROM Item
			RIGHT JOIN Listing ON Item.ItemID = Listing.FK_Item_ItemID
			WHERE Listing.ListingID = :listing_id;
		");
		$statement0->bindValue(":listing_id", $listing_id, PDO::PARAM_INT);
		$statement0->execute();
		$item_id = $statement0->fetchColumn();
		
		$statement = $this->db->prepare("
			SELECT *
			FROM Item
			WHERE ItemID = :item_id;
		");
		$statement->bindValue(":item_id", $item_id, PDO::PARAM_INT);
		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

?>

