<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 13/03/2017
 * Time: 15:13
 */
namespace Wastetopia\Model;
use Wastetopia\Model\DB;
use PDO;

/**
 * Class NotificationModel - Used for any notifications (Currently unread messages and unseen requests)
 * @package Wastetopia\Model
 */
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
        $reader = new UserCookieReader();
        return $reader->get_user_id();
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

        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchColumn();
    }


    /**
     * Returns the total number of unread messages this user has
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
                    AND `Message`.`Giver_Or_Receiver`= 1))      
            AND `Message`.`Read` = 0;   
        ");

        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->bindValue(":userID2", $userID, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchColumn();
    }
    
    
    /**
    * Returns array with all notifications in
    * @param $json (defaults to 0, set to 1 if you need the array in JSON format)
    * @return array in form ("requestNotifications => numberOfUnseenRequests, "messageNotifications" => numberOfUnseenMessages)
    */
    function getAll($json=0){
        $requestNotifications = $this->requestNotifications();
        $messageNotifications = $this->messageNotifications();
        
        $notifications = array("requestNotifications" => $requestNotifications, "messageNotifications" => $messageNotifications);
        
        if($json){
            return json_encode($notifications);   
        }else{
            return $notifications;
        }
    }


}
