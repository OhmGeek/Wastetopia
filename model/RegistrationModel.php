<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 03/03/2017
 * Time: 10:06
 */
namespace Wastetopia\Model;
use Wastetopia\Model\DB;
use PDO;

/**
 * Class RegistrationModel - Used for Registration page
 * @package Wastetopia\Model
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


    /**
     * Deletes any user with the given name. ONLY FOR TESTING. REMOVE LATER.
     * @param $firstName
     * @param $lastName
     * @return bool
     */
    function deleteUserByName($firstName, $lastName){
        $statement = $this->db->prepare("
            DELETE FROM `User`
                WHERE `Forename` = :firstName
                AND `Surname` = :lastName
        ");
        
        $statement->bindValue(":firstName", $firstName, PDO::PARAM_STR);
        $statement->bindValue(":lastName", $lastName, PDO::PARAM_STR);
        
        $statement->execute();
        return True;
      
    }


    /**
     * Deletes a user with the given email address
     * @param $email
     * @return bool
     */
    function deleteUser($email){
        $statement = $this->db->prepare("
            DELETE FROM `User`
                WHERE `Email_Address` = :email
        ");
        
        $statement->bindValue(":email", $email, PDO::PARAM_STR);
        $statement->execute();
        return True;
      
    }
    
    
//    private function getLastInsertID()
//    {
//        $statement = $this->db->prepare("
//            SELECT LAST_INSERT_ID()
//         ");
//        $statement->execute();
//        return $statement->fetchColumn();
//    }


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
        
        
        $result = count($statement->fetchAll(PDO::FETCH_ASSOC));
        return $result > 0;
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
    function addMainUserDetails($forename, $surname, $email, $passwordHash, $salt, $pictureURL, $verificationCode)
    {
        //Need to add PictureURL when we have default
        $statement = $this->db->prepare("
            INSERT INTO `User` (`Forename`, `Surname`, `Email_Address`, `Password_Hash`, `Salt`, `Verification_Code`)
            VALUES (:forename, :surname, :email, :passwordHash, :salt, :code); 
        ");

        $statement->bindValue(":forename", $forename, PDO::PARAM_STR);
        $statement->bindValue(":surname", $surname, PDO::PARAM_STR);
        $statement->bindValue(":email", $email, PDO::PARAM_STR);
        $statement->bindValue(":passwordHash", $passwordHash, PDO::PARAM_STR);
        $statement->bindValue(":salt", $salt, PDO::PARAM_STR);
        $statement->bindValue(":code", $verificationCode, PDO::PARAM_STR);
        //$statement->bindValue(":pictureURL", $pictureURL, PDO::PARAM_STR);

        $statement->execute();

        return true;
    }



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
//         if ($pictureURL == NULL) {
//             // $pictureURL = DEFAULT_IMAGE;
//         }

        $salt = $this->generateSalt();
        $passwordHash = hash('sha256',$salt.$password);

        $verificationCode = $this->generateSalt(); // New random string for verification
        
        //Add user's details
        $result= $this->addMainUserDetails($forename, $surname, $email, $passwordHash, $salt, $pictureURL, $verificationCode);

        return $result;
    }

    
    /**
    * Returns the verification code to send to the given email
    * @param $email
    * @return mixed (-1 if email not in DB, String otherwise)
    */
    function getVerificationCode($email){
        $statement = $this->db->prepare("
            SELECT `Verification_Code`
            FROM `User`
            WHERE `User`.`Email_Address` = :email;
        ");

        $statement->bindValue(":email", $email, PDO::PARAM_STR);        
        $statement->execute();
        
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);        
        $result = count($results);
        
        if($result > 0){
            return $results["0"]["Verification_Code"];
        }else{
            return -1;
        }

    }
    
    
    /**
     * Generates a random Salt string (in Hexadecimal) between 30 and 40 bytes in length
     * @return string
     */
    function generateSalt(){
        $salt = random_bytes(mt_rand(30, 40));
        return bin2hex($salt);
    }
    
    
    /** Changes the Active flag for user with the given verification code
    * @param $verificationCode
    * @return bool
    */
    function verifyUser($verificationCode){
        $statement = $this->db->prepare("
                UPDATE `User`
                SET `Active` = 1
                WHERE `User`.`Verification_Code` = :code;
                
            ");

        $statement->bindValue(":code", $verificationCode, PDO::PARAM_STR);
        $result = $statement->execute();
        return $result       
    }
}
