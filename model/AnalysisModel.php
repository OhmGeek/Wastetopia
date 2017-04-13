<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 13/03/2017
 * Time: 15:06
 */

namespace Wastetopia\Model;
use Wastetopia\Model\DB;
use PDO;

class AnalysisModel
{

    /**
     * AnalysisModel constructor.
     */
    public function __construct()
    {
        $this->db = DB::getDB();
    }

    /**
     * Returns the ID of the user currently logged in
     * @return string
     */
    private function getUserID()
    {
        //$reader = new UserCookieReader();
        //return $reader->get_user_id();
        return 6; //Hardcoded for now
    }

   
    /**
     * Gets a list of Tag Names along with their frequencies for current user's listings
     * @param $categoryID (optional - defaults to -1 to search through all categories. Specificy categoryID to only search by one of them)
     * @return array
     */
    function getTagFrequenciesForListings($categoryID = -1)
    {
        $userID = $this->getUserID();
        $categorySQL = ($categoryID == -1) ? "" : "AND `Tag`.`FK_Category_Category_ID` = "+$categoryID;
        $statement = $this->db->prepare("
        SELECT `Tag`.`Name`, `Tag`.`TagID`, COUNT(*) as `Count`
                FROM `Tag` 
                JOIN `ItemTag` ON `ItemTag`. `FK_Tag_TagID` = `Tag`.`TagID`
                JOIN `Item` ON `Item`.`ItemID` = `ItemTag`.`FK_Item_ItemID`
                JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
                JOIN `User` ON `UserID` = `Listing`.`FK_User_UserID`
                WHERE `User`.`UserID` = :userID
                :categoryRestriction
                AND `Tag`.`FK_Category_Category_ID` = :categoryID
                GROUP BY `Tag`.`Name`
                ORDER BY `Count` DESC;");

        $statement->bindValue(":userID", $userID, PDO::PARAM_STR);
        $statement->bindValue(":categoryRestriction", $cateogorySQL, PDO::PARAM_STR):
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
}
    
    /**
     * Gets a list of Tag Names along with their frequencies for items the user has received 
     * @param $categoryID (optional - defaults to 1)
     * @return array
     */
    function getTagFrequenciesForTransactions($categoryID = 1)
    {
        $userID = $this->getUserID();

        $statement = $this->db->prepare("
        SELECT `Tag`.`Name`,  `Tag`.`TagID`, SUM(`ListingTransaction`.`Quantity`) as `Count`
                FROM `Tag` 
                JOIN `ItemTag` ON `ItemTag`. `FK_Tag_TagID` = `Tag`.`TagID`
                JOIN `Item` ON `Item`.`ItemID` = `ItemTag`.`FK_Item_ItemID`
                JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
                JOIN `ListingTransaction` ON `ListingTransaction`.`FK_Listing_ListingID` = `Listing`.`ListingID`
                JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
                JOIN `User` ON `UserID` = `Transaction`.`FK_User_UserID`
                WHERE `User`.`UserID` = :userID
                AND `Tag`.`FK_Category_Category_ID` = :categoryID
                AND `ListingTransaction`.`Success` = 1
                GROUP BY `Tag`.`Name`
                ORDER BY `Count` DESC;");

        $statement->bindValue(":userID", $userID, PDO::PARAM_STR);
        $statement->bindValue(":categoryID", $categoryID, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
