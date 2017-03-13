<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 13/03/2017
 * Time: 15:06
 */

namespace Wastetopia\Model;
use DB;

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
        return 20; //Hardcoded for now
    }


    /**
     * Gets a list of Tag Names along with their frequencies
     * @return array
     */
    function getTagFrequencies()
    {
        $userID = $this->getUserID();

        $statement = $this->db->prepare("
        SELECT `Tag`.`Name`, COUNT(*) as Count
                FROM `Tag` 
                JOIN `ItemTag` ON `ItemTag`. `FK_Tag_TagID` = `Tag`.`TagID`
                JOIN `Item` ON `Item`.`ItemID` = `ItemTag`.`FK_Item_ItemID`
                JOIN `Listing` ON `Listing`.`FK_Item_ItemID` = `Item`.`ItemID`
                JOIN `User` ON `UserID` = `Listing`.`FK_User_UserID`
                WHERE `User`.`UserID` = 2
                AND `Tag`.`FK_Category_Category_ID` = 1
                GROUP BY `Tag`.`Name`;");

        $statement->bindValue(":userID", $userID, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
}
}