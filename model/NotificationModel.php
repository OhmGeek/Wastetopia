<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 13/03/2017
 * Time: 15:13
 */
namespace Wastetopia\Model;
use DB;

class NotificationModel
{

    /**
     * NotificationModel constructor.
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
     * Returns the number of unseen transactions (requests) this user has on their listings
     * @return integer
     */
    function requestNotifications()
    {
        $userID = $this->getUserID();

        $statement = $this->db->prepare("
        SELECT COUNT(*) AS `Count`
        FROM `ListingTransaction` 
        JOIN `Listing` ON `Listing`.`ListingID` = `ListingTransaction`.`FK_Listing_ListingID`
        JOIN `User` ON `User`.`UserID` = `Listing`.`FK_User_UserID`
        WHERE `UserID` = :userID
        AND `ListingTransaction`.`Viewed` = 0; 
        ");

        $statement->bindValue(":userID", $userID, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
    }


    /**
     * Returns the number of total unread messages this user has
     * @return integer
     */
    function messageNotifications()
    {
        $userID = $this->getUserID();

        $statement = $this->db->prepare("
        SELECT COUNT(*) AS `Count`
        FROM `Message`
        JOIN `Conversation` ON `Conversation`.`ConversationID` = `Message`.`FK_Conversation_ConversationID`
        WHERE ((`Conversation`.`FK_User_ReceiverID` = :userID       
            AND `Message`.`Giver_Or_Receiver` = 0)            
            OR (NOT(`Conversation`.`FK_User_ReceiverID` = :userID2) 
                AND `Message`.`Giver_Or_Receiver`= 1)))      
        AND `Message`.`Read` = 0;   
        ");

        $statement->bindValue(":userID", $userID, PDO::PARAM_STR);
        $statement->bindValue(":userID2", $userID, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
    }


}
