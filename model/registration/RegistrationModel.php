<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 03/03/2017
 * Time: 10:06
 */
class RegistrationModel
{

    /**
     * RegistrationModel constructor.
     */
    public function __construct()
    {
        $this->db = DB::getDB();
    }


    private function getLastInsertID()
    {
        $statement = $this->db->prepare("
            SELECT LAST_INSERT_ID()
         ");
        $statement->execute();
        return $statement->fetchColumn();
    }


    /**
     * Checks whether the given email address is already in the database
     * @param $email
     * @return bool (True if email already exists)
     */
    function checkEmailExists($email)
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM `User`
            WHERE `User`.`Email_Address` = :email;
        ");

        $statement->bindValue(":email", $email, PDO::PARAM_STR);

        $statement->execute();

        return (count($statement->fetchAll(PDO::FETCH_ASSOC)) > 0);
    }


    /**
     * Adds the basic details for a user (name, email, passwordHash, salt)
     * @param $forename
     * @param $surname
     * @param $email
     * @param $passwordHash
     * @param $salt (for the password)
     * @param $pictureURL
     * @return The ID of the user added
     */
    function addMainUserDetails($forename, $surname, $email, $passwordHash, $salt, $pictureURL)
    {
        //Need to calculate password hash with salt
        $statement = $this->db->prepare("
            INSERT INTO `User` (`Forename`, `Surname`, `Email_Address` `Password_Hash`, `Salt`, `Picture_URL`)
            VALUES (:forename, :surname, :email, :passwordHash, :salt, :pictureURL); 
        ");

        $statement->bindValue(":forename", $forename, PDO::PARAM_STR);
        $statement->bindValue(":surname", $surname, PDO::PARAM_STR);
        $statement->bindValue(":email", $email, PDO::PARAM_STR);
        $statement->bindValue(":passwordHash", $passwordHash, PDO::PARAM_STR);
        $statement->bindValue(":salt", $salt, PDO::PARAM_STR);
        $statement->bindValue(":pictureURL", $pictureURL, PDO::PARAM_STR);

        $statement->execute();

        return true;
    }


//    /**
//     * Adds a picture for a given user
//     * @param $userID
//     * @param $pictureURL
//     * @return bool
//     */
//    function addUserPicture($userID, $pictureURL)
//    {
//        $statement = $this->db->prepare("
//            UPDATE `User`
//            SET `Picture_URL` = :url
//            WHERE `User`.`UserID` = :userID
//        ");
//
//        $statement->bindValue(":url", $pictureURL, PDO::PARAM_STR);
//        $statement->bindValue(":userID", $userID, PDO::PARAM_INT);
//
//
//        $statement->execute();
//
//        return true;
//
//    }


    /**
     * Adds a user and then adds a picture for that user
     * @param $forename
     * @param $surname
     * @param $email
     * @param $password
     * @param null $pictureURL
     * @return bool (True if successful)
     */
    function addUser($forename, $surname, $email, $password, $pictureURL = NULL)
    {
        //If no picture specified, add a Default image
        if ($pictureURL !== NULL) {
            // $pictureURL = DEFAULT_IMAGE;
        }

//        $salt = ;
//        $passwordHash = ;

        //Add user's details
        $result= $this->addMainUserDetails($forename, $surname, $email, $passwordHash, $salt, $pictureURL);

        return $result;
    }
}