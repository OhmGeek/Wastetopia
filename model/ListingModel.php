<?php

//BASICALLY UNTESTED

namespace Wastetopia\Model;
use PDO;
use Wastetopia\Model\DB;


class ListingModel
{
	function __construct()
	{
		$this->db = DB::getDB();
	}

	function getListingInfo($listing_id){
		$statement = $this->db->prepare("
			SELECT FK_Location_LocationID, FK_Item_ItemID, FK_User_UserID, Quantity, Time_Of_Creation, Active 
			FROM Listing
			WHERE ListingID = :listing_id;
		");
		$statement->bindValue(":listing_id", $listing_id, PDO::PARAM_INT);
		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

?>

