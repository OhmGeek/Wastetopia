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
        return 2; //Hardcoded for now
    }

   
    /**
     * Gets a list of Tag Names along with their frequencies for current user's listings
     * @param $categoryIDArray (Optional - defaults to empty array => checks all category IDs. Array of CategoryIDs to match)
     * @return array
     */
    function getTagFrequenciesForListings($categoryIDArray = array())
    {
        $userID = $this->getUserID();
  
        $sql = "SELECT `Tag`.`Name`, `Tag`.`TagID`, COUNT(*) as `Count`
                FROM `Tag` 
                JOIN `ItemTag` ON `ItemTag`. `FK_Tag_TagID` = `Tag`.`TagID`
                JOIN `Item` ON `Item`.`ItemID` = `ItemTag`.`FK_Item_ItemID`
                JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
                JOIN `User` ON `UserID` = `Listing`.`FK_User_UserID`
                WHERE `User`.`UserID` = :userID
                ";
        
        if(count($categoryIDArray) != 0){
            $sql .= "AND ("; 
            // Add the first CategoryID check
            $categoryID = $categoryIDArray[0];
            $sql .= "`Tag`.`FK_Category_Category_ID` = :category"+$categoryID;
            
            // Add all of the CategoryIDs to the SQL statement
            for($x = 1; $x < count($categoryID); $x ++){
                $categoryID = $categoryIDArray[$x];
                // Add this with OR so it can match any of them
                $sql .= "OR `Tag`.`FK_Category_Category_ID` = :category"+$categoryID;
            }
            
            $sql .= ")"; // End the category section
        }
        
        // Group into Tag Name and order by count in descending order
        $sql .= "GROUP BY `Tag`.`Name`
                ORDER BY `Count` DESC;";
        
        //Prepare the SQL statement
        $statement = $this->db->prepare($sql); 

        $statement->bindValue(":userID", $userID, PDO::PARAM_STR);
        
        if(count($categoryIDArray) != 0){
            // Bind all of the categoryIDs to the statement
            for($x = 0; $x < count($categoryID); $x ++){
                $categoryID = $categoryIDArray[$x];
                $statement->bindValue(":category"+$categoryID, $categoryID, PDO::PARAM_STR);
            }
            
        }
        
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
}
    
    /**
     * Gets a list of Tag Names along with their frequencies for items the user has received 
     * @param $categoryIDArray (Optional - defaults to empty array => checks all category IDs. Array of CategoryIDs to match)
     * @return array
     */
    function getTagFrequenciesForTransactions($categoryIDArray = array())
    {
        $userID = $this->getUserID();
        
        $sql = "SELECT `Tag`.`Name`,  `Tag`.`TagID`, SUM(`ListingTransaction`.`Quantity`) as `Count`
                FROM `Tag` 
                JOIN `ItemTag` ON `ItemTag`. `FK_Tag_TagID` = `Tag`.`TagID`
                JOIN `Item` ON `Item`.`ItemID` = `ItemTag`.`FK_Item_ItemID`
                JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
                JOIN `ListingTransaction` ON `ListingTransaction`.`FK_Listing_ListingID` = `Listing`.`ListingID`
                JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
                JOIN `User` ON `UserID` = `Transaction`.`FK_User_UserID`
                WHERE `User`.`UserID` = :userID
                ";
        
        if(count($categoryIDArray) != 0){
            $sql .= "AND ("; 
            // Add the first CategoryID check
            $categoryID = $categoryIDArray[0];
            $sql .= "`Tag`.`FK_Category_Category_ID` = :category"+$categoryID;
            
            // Add all of the CategoryIDs to the SQL statement
            for($x = 1; $x < count($categoryID); $x ++){
                $categoryID = $categoryIDArray[$x];
                // Add this with OR so it can match any of them
                $sql .= "OR `Tag`.`FK_Category_Category_ID` = :category"+$categoryID;
            }
            
            $sql .= ")"; // End the category section
        }
        
        // Group into Tag Name and order by count in descending order
        $sql .= "AND `ListingTransaction`.`Success` = 1
                GROUP BY `Tag`.`Name`
                ORDER BY `Count` DESC;";
        
        //Prepare the SQL statement
        $statement = $this->db->prepare($sql); 

        $statement->bindValue(":userID", $userID, PDO::PARAM_STR);
        
        if(count($categoryIDArray) != 0){
            // Bind all of the categoryIDs to the statement
            for($x = 0; $x < count($categoryID); $x ++){
                $categoryID = $categoryIDArray[$x];
                $statement->bindValue(":category"+$categoryID, $categoryID, PDO::PARAM_STR);
            }
            
        }
        
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
