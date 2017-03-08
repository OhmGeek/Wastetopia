<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 08/03/2017
 * Time: 12:08
 */

namespace Wastetopia\Model;
use DB;

class PopularityModel
{
    /**
     * PopularityModel constructor.
     */
    public function __construct()
    {
        $this->db = DB::getDB();
    }


    /**
     * Returns the ID of the user currently logged in
     * @return string
     */
    function getUserID()
    {

//        $reader = new UserCookieReader();
//        return $reader->get_user_id();
        return 20; //Hardcoded for now
    }


    /**
     * Gets the number of ratings and average rating of the given user
     * @return array
     */
    function getUserRatingDetails($userID)
    {

        $statement = $this->db->prepare("
            SELECT `User`.`Number_Of_Ratings`, `User`.`Mean_Rating_Percent`
            FROM `User`
            WHERE `User`.`UserID` = :userID
        ");

        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Sets new values for ratings for a given user
     * @param $userID
     * @param $meanRating
     * @param $numberOfRatings
     * @return bool
     */
    function setUserRating($userID, $meanRating, $numberOfRatings)
    {

        $statement = $this->db->prepare("
            SELECT `User`
            SET `Number_Of_Ratings` = :numberOfRatings
            AND `Mean_Rating_Percent` = :meanRating
            WHERE `UserID` = :userID
        ");

        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
        $statement->bindValue(":numberOfRatings", $numberOfRatings, PDO::PARAM_INT);
        $statement->bindValue(":meanRating", $meanRating, PDO::PARAM_INT);

        $statement->execute();

        return true;
    }


    /**
     * Main function to add a new rating for a given user
     * @param $userID
     * @param $rating
     */
    function addNewRating($userID, $rating){
        //Get original details
        $originalRatingDetails = $this->getUserRatingDetails($userID);
        $originalNumberOfRatings = $originalRatingDetails[0]["Number_Of_Ratings"];
        $originalMeanRatingPercent = $originalRatingDetails[0]["Mean_Rating_Percent"];

        //Calculate original total rating
        $originalTotal = $originalNumberOfRatings*$originalMeanRatingPercent;

        //Add new rating
        $newTotal = $originalTotal + $rating;

        //Calculate new mean value
        $newNumberOfRatings = $originalNumberOfRatings + 1;
        $newMeanRatingPercent = $newTotal/$newNumberOfRatings;

        //Set values in Database
        $this->setUserRating($userID, $newMeanRatingPercent, $newNumberOfRatings);
    }

}