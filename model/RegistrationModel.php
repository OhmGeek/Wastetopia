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

class RegistrationModel
{

    /**
     * RegistrationModel constructor.
     */
    public function __construct()
    {
        $this->db = DB::getDB();
    }


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
//         if ($pictureURL == NULL) {
//             // $pictureURL = DEFAULT_IMAGE;
//         }

        $salt = $this->generateSalt();
        $passwordHash = hash('sha256',$salt.$password);

        $verificationCode = $this->generateSalt(); // New random string for verification
        print_r("CODE: ".$verificationCode);
      //  print_r("FROM MODEL");
      //  print_r($forename);
      //  print_r($surname);
      //  print_r("EMAIL:: ".$email);
      //  print_r("PASSWORD:: ".$password);
      //  print_r($pictureURL);
      //  print_r("SALT:: ".$salt);
      //  print_r("HASH:: ".$passwordHash);
        
        //Add user's details
        $result= $this->addMainUserDetails($forename, $surname, $email, $passwordHash, $salt, $pictureURL, $verificationCode);
        
        
        //$result = false;
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
        
        print_r($statement->fetchAll(PDO::FETCH_ASSOC));
        
        $result = count($statement->fetchAll(PDO::FETCH_ASSOC));
        
        if($result > 0){
          return $statement->fetchColumn();   
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
            SELECT *
            FROM `User`
            WHERE `User`.`Verification_Code` = :code
            AND `User`.`Active` = 0;
        ");

        $statement->bindValue(":code", $verificationCode, PDO::PARAM_STR);
        $statement->execute();
        
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        if(count($result) > 0){
             $userID = $result["UserID"]; // UserID of new user
             $statement2 = $this->db->prepare("
                UPDATE `User`
                SET `Active` = 1
                WHERE `User`.`UserID` = :userID;
                
            ");

            $statement->bindValue(":userID", $userID, PDO::PARAM_INT);

            $statement->execute();
            return True;
        }else{
            return False;
        }
    }
}
