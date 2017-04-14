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
            $sql .= "`Tag`.`FK_Category_Category_ID` = :category".$categoryID;
            
            // Add all of the CategoryIDs to the SQL statement
            for($x = 1; $x < count($categoryIDArray); $x ++){
                $categoryID = $categoryIDArray[$x];
                // Add this with OR so it can match any of them
                $sql .= "OR `Tag`.`FK_Category_Category_ID` = :category".$categoryID;
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
            for($x = 0; $x < count($categoryIDArray); $x ++){
                $categoryID = $categoryIDArray[$x];
                $statement->bindValue(":category".$categoryID, $categoryID, PDO::PARAM_STR);
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
            $sql .= "`Tag`.`FK_Category_Category_ID` = :category".$categoryID;
            
            // Add all of the CategoryIDs to the SQL statement
            for($x = 1; $x < count($categoryIDArray); $x ++){
                $categoryID = $categoryIDArray[$x];
                // Add this with OR so it can match any of them
                $sql .= "OR `Tag`.`FK_Category_Category_ID` = :category".$categoryID;
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
            for($x = 0; $x < count($categoryIDArray); $x ++){
                $categoryID = $categoryIDArray[$x];
                $statement->bindValue(":category".$categoryID, $categoryID, PDO::PARAM_STR);
            }
            
        }
        
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    
//     /**
//     * Returns the frequencies of Names of items user is giving away
//     * Frequncy calculated as SUM of current quantities
//     * @return array
//     */
//     function getNameFrequenciesSending(){
//         $userID = $this->getUserID();
        
//         $statement = $this->db->prepare("
//             SELECT `Item`.`ItemID`, `Item`.`Name`, SUM(`Listing`.`Quantity`) AS `Count`
//             FROM `Item`
//             JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
//             JOIN  `User` ON `Listing`.`FK_User_UserID` = `User`.`UserID`
//             WHERE `User`.`UserID` = :userID
//             GROUP BY `Item`.`Name`
//             ORDER BY `Count` DESC;
//         ");
        
//         $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
//         $statement->execute();
        
//         return $statement->fetchAll(PDO::FETCH_ASSOC);
        
//     }
    
//     /**
//     * Returns the frequencies of Names of items user is giving away
//     * Frequncy calculated as SUM of quantities for successful transactions
//     * @return array
//     */
//     function generateNameFrequenciesFromSendingTransactions(){
//         $userID = $this->getUserID();
        
//         $statement = $this->db->prepare("
//             SELECT `Item`.`ItemID`, `Item`.`Name`, SUM(`ListingTransaction`.`Quantity`) AS `Transactions_Quantity`
//             FROM `ListingTransaction`
//             JOIN `Listing` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
//             JOIN `User` ON `User`.`UserID` = `Listing`.`FK_User_UserID`
//             JOIN `Item` ON `Item`.`ItemID` = `Listing`.`FK_Item_ItemID`
//             WHERE `User`.`UserID` = :userID
//             AND `ListingTransaction`.`Success` = 1     
//             GROUP BY `Item`.`Name`
//             ORDER BY `Transactions_Quantity` DESC
//         ");
        
//         $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
//         $statement->execute();
        
//         return $statement->fetchAll(PDO::FETCH_ASSOC);
//     }

    /**
    * Returns the frequencies of Names of items user is giving away
    * Frequncy calculated as SUM of quantities for successful transactions + SUM of current quantity left
    * @return array
    */
    function getTotalNameFrequenciesSending(){
       $userID = $this->getUserID();

        $statement = $this->db->prepare("
            SELECT `Item`.`ItemID`, `Item`.`Name`, (SUM(`Listing`.`Quantity`) + SUM(`Inner`.`Transactions_Quantity`)) AS `Count`
            FROM `Item`
            JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
            JOIN  `User` ON `Listing`.`FK_User_UserID` = `User`.`UserID`
            LEFT JOIN (SELECT `Item`.`ItemID`, `Item`.`Name`, `ListingTransaction`.`Quantity` AS `Transactions_Quantity`
                        FROM `ListingTransaction`
                        JOIN `Listing` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
                        JOIN `User` ON `User`.`UserID` = `Listing`.`FK_User_UserID`
                        JOIN `Item` ON `Item`.`ItemID` = `Listing`.`FK_Item_ItemID`
                        WHERE `ListingTransaction`.`Success` = 1) AS `Inner` 
                ON `Inner`.`ItemID` = `Item`.`ItemID`
            WHERE `User`.`UserID` = :userID
            GROUP BY `Item`.`Name`
            ORDER BY `Count` DESC;
        ");
        
        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->execute();


        return $statement->fetchAll(PDO::FETCH_ASSOC); 
    }



    /**
     * Returns the frequencies of Names of items user is giving away
     * Frequncy calculated as SUM of quantities for successful transactions + SUM of current quantity left
     * @return array
     */
    function getTotalNameFrequenciesReceiving(){
        $userID = $this->getUserID();

        $statement = $this->db->prepare("
           SELECT `Item`.`ItemID`, `Item`.`Name`, SUM(`ListingTransaction`.`Quantity`) AS `Count`
                FROM `Item`
				JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
				JOIN `ListingTransaction` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
                JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
                JOIN `User` ON `User`.`UserID` = `Transaction`.`FK_User_UserID`
                WHERE `ListingTransaction`.`Success` = 1
				AND `User`.`UserID` = :userID
				GROUP BY `Item`.`Name`
            ORDER BY `Count` DESC;
        ");

        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Returns array of category names and IDs
     * @return array
     */
    function getCategoryNamesAndIDs()
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM Category;
        ");

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns array of TagNames for the given category
     * @param $categoryID
     * @return array
     */
    function getTagNamesFromCategory($categoryID){
        $statement = $this->db->prepare("
            SELECT `Tag`.`Name`
            FROM `Tag`
            WHERE `Tag`.`FK_Category_Category_ID` = :categoryID;
        ");

        $statement->bindValue(":categoryID", $categoryID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
