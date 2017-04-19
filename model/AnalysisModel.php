<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 13/03/2017
 * Time: 15:06
 */

namespace Wastetopia\Model;
use Wastetopia\Model\DB;
use Wastetopia\Model\UserCookieReader;
use PDO;


/**
 * Class AnalysisModel - Functions to get frequencies of Tags and Names
 * @package Wastetopia\Model
 */
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
       $reader = new UserCookieReader();
        return $reader->get_user_id();
    }

   
    /**
     * Gets a list of Tag Names along with their frequencies for current user's listings (includes current quantity and transactions quantity)
     * @param $categoryIDArray - Array of CategoryIDs to match: Optional - defaults to empty array => checks all category IDs
     * @return array - In Descending order by Frequency
     */
    function getTagFrequenciesForListings($userID = null, $categoryIDArray = array())
    {
        if($userID == null) {
            $userID = $this->getUserID();
        }

        // Start sql query
        // Inner table gets Tags with their quantity in successful transactions
        // Outer table gets Tags with their current quantity in user's listing
        // Join Tables to get total quantity for all time for each Tag
        $sql = "SELECT `Tag`.`Name`, `Tag`.`TagID`, (SUM(COALESCE(`Listing`.`Quantity`,0)) + SUM(COALESCE(`Inner`.`Transactions_Quantity`,0))) AS `Count`
                FROM `Tag` 
                JOIN `ItemTag` ON `ItemTag`. `FK_Tag_TagID` = `Tag`.`TagID`
                JOIN `Item` ON `Item`.`ItemID` = `ItemTag`.`FK_Item_ItemID`
                JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
                JOIN `User` ON `UserID` = `Listing`.`FK_User_UserID`
                JOIN (SELECT `Item`.`ItemID`, `Item`.`Name`, `ListingTransaction`.`Quantity` AS `Transactions_Quantity`
                                        FROM `ListingTransaction`
                                        JOIN `Listing` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
                                        JOIN `User` ON `User`.`UserID` = `Listing`.`FK_User_UserID`
                                        JOIN `Item` ON `Item`.`ItemID` = `Listing`.`FK_Item_ItemID`
                                        WHERE `ListingTransaction`.`Success` = 1) AS `Inner`
                ON `Inner`.`ItemID` = `Item`.`ItemID`
                WHERE `User`.`UserID` = :userID ";

        // Add TagID matches to Query
        // Using OR so can match some of them
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

        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
	    return $results;
}
    
    /**
     * Gets a list of Tag Names along with their frequencies for items the user has received 
     * @param $categoryIDArray (Optional - defaults to empty array => checks all category IDs. Array of CategoryIDs to match)
     * @return array - In descending order by frequency
     */
    function getTagFrequenciesForTransactions($categoryIDArray = array())
    {

        $userID = $this->getUserID();
        
        $sql = "SELECT `Tag`.`Name`,  `Tag`.`TagID`, SUM(COALESCE(`ListingTransaction`.`Quantity`), 0) as `Count`
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
    

    /**
    * Returns the frequencies of Names of items user is giving away
    * Frequncy calculated as SUM of quantities for successful transactions + SUM of current quantity left
    * @return array - In descending Order by frequency
    */
    function getTotalNameFrequenciesSending($userID = null){
        if ($userID == null) {
            $userID = $this->getUserID();
        }

        print_r("Sending user: ".$userID);

        // Inner table gets Items with their quantity in successful transactions
        // Outer table gets Items with their current quantity in user's listing
        // Join Tables to get total quantity for all time for each item
        // Then grouped by Name and Ordered By Count
        $statement = $this->db->prepare("
            SELECT `Item`.`ItemID`, `Item`.`Name`, (SUM(COALESCE(`Listing`.`Quantity`, 0)) + SUM(COALESCE(`Inner`.`Transactions_Quantity`,0))) AS `Count`
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

	   $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }



    /**
     * Returns the frequencies of Names of items user is giving away
     * Frequncy calculated as SUM of quantities for successful transactions + SUM of current quantity left
     * @return array - In descending order by frequency
     */
    function getTotalNameFrequenciesReceiving(){
        $userID = $this->getUserID();

        print_r("User receiving: ".$userID);

        $statement = $this->db->prepare("
           SELECT `Item`.`ItemID`, `Item`.`Name`, SUM(COALESCE(`ListingTransaction`.`Quantity`, 0)) AS `Count`
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

        print_r("Receiving names: ");
        print_r($statement->fetchAll(PDO::FETCH_ASSOC));
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


    /**
     * Gets the total number of completed listings the user has given
     * If neither year nor month are specified then all completed listings are evaluated, regardless of date
     * @param $year The subject year.
     * @param $month The subject month
     * @param $timespan How many months to evaluate, default 1
     * @return Integer
     */
    function getNumberOfCompletedGiving($year = -1, $month = -1, $timespan = 1)
    {
        $userID = $this->getUserID();
        $end_year = $year;
        $end_month = $month;
        if($year == -1 && $month == -1){
            $statement = $this->db->prepare("
				SELECT COUNT(*) as `Count`
				FROM `Listing`
				JOIN `ListingTransaction` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
				JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
				JOIN `User` ON `User`.`UserID` = `Listing`.`FK_User_UserID`
				WHERE `User`.`UserID` = :userID
				AND `ListingTransaction`.`Success` = 1;
			");
            $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchColumn();
        }
        else if($year != -1 && $month != -1){
            if($month + $timespan >= 13){
                while($timespan >= 1){
                    if($end_month == 12){
                        $end_month = 1;
                        $end_year = $end_year + 1;
                        $timespan = $timespan - 1;
                    }
                    else{
                        $end_month = $end_month + 1;
                        $timespan = $timespan - 1;
                    }
                }
            }
            else{
                $end_month = $end_month + $timespan;
            }

            $month = sprintf("%02d", $month);
            $end_month = sprintf("%02d", $end_month);

            $statement = $this->db->prepare("
				SELECT COUNT(*) as `Count`
				FROM `Listing`
				JOIN `ListingTransaction` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
				JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
				JOIN `User` ON `User`.`UserID` = `Listing`.`FK_User_UserID`
				WHERE `User`.`UserID` = :user_id
				AND `ListingTransaction`.`Success` = 1
				AND Time_Of_Acceptance >= :start_date
				AND Time_Of_Acceptance < :end_date;
			");

            $statement->bindValue(":user_id", $userID);
            $start_date = "" . $year . "-" . $month . "-01 00:00:00";
            $end_date = "" . $end_year . "-" . $end_month . "-01 00:00:00";
            $statement->bindValue(":start_date", $start_date, PDO::PARAM_STR);
            $statement->bindValue(":end_date", $end_date, PDO::PARAM_STR);

            $statement->execute();
            $errors = $statement->errorInfo();
            return $statement->fetchColumn() . " + errors: " . $errors[0] . $errors[1] . $errors[2] . "start_date: " . $start_date . "end_date: " . $end_date;
        }
        else{
            error_log("Received failed");
            return false;
        }

    }

    /**
     * Gets the total number of completed listings the user has received
     * If neither year nor month are specified then all completed listings are evaluated, regardless of date
     * @param $year The subject year
     * @param $month The subject month
     * @param $timespan How many months to evaluate, default 1
     * @return Integer
     */
    function getNumberOfCompletedReceived($year = -1, $month = -1, $timespan = 1)
    {
        $userID = $this->getUserID();
        $end_year = $year;
        $end_month = $month;
        if($year == -1 && $month == -1){
            $statement = $this->db->prepare("
				SELECT COUNT(*) as `Count`
				FROM `Listing`
				JOIN `ListingTransaction` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
				JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
				WHERE `Transaction`.`FK_User_UserID` = :userID
				AND `ListingTransaction`.`Success` = 1;
			");
            $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchColumn();
        }
        else if($year > -1 && $month > -1){
            if($month + $timespan >= 13){
                while($timespan >= 1){
                    if($end_month == 12){
                        $end_month = 1;
                        $end_year = $end_year + 1;
                        $timespan = $timespan - 1;
                    }
                    else{
                        $end_month = $end_month + 1;
                        $timespan = $timespan - 1;
                    }
                }
            }
            else{
                $end_month = $end_month + $timespan;
            }
            $month = sprintf("%02d", $month);
            $end_month = sprintf("%02d", $end_month);
            $statement = $this->db->prepare("
				SELECT COUNT(*) as `Count`
				FROM `Listing`
				JOIN `ListingTransaction` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
				JOIN `Transaction` ON `Transaction`.`TransactionID` = `ListingTransaction`.`FK_Transaction_TransactionID`
				WHERE `Transaction`.`FK_User_UserID` = :user_id
				AND `ListingTransaction`.`Success` = 1
				AND Time_Of_Acceptance >= :start_date
				AND Time_Of_Acceptance < :end_date;
			");

            $statement->bindValue(":user_id", $userID);
            $start_date = "" . $year . "-" . $month . "-01 00:00:00";
            $end_date = "" . $end_year . "-" . $end_month . "-01 00:00:00";
            $statement->bindValue(":start_date", $start_date, PDO::PARAM_STR);
            $statement->bindValue(":end_date", $end_date, PDO::PARAM_STR);

            $statement->execute();
            $errors = $statement->errorInfo();
            return $statement->fetchColumn() . " + errors: " . $errors[0] . $errors[1] . $errors[2] . "start_date: " . $start_date . "end_date: " . $end_date;
        }
        else{
            return "Neither $year nor $month were equal to -1 and were not >= -1";
            $error_log("Received failed");
            return false;
        }

    }

}
