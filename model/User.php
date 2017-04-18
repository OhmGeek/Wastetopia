<?php

namespace Wastetopia\Model;

use PDO;

class User {

    public static function verify_credentials($username, $password) {
        $db = DB::getDB();

        $statement = $db->prepare("SELECT Password_Hash, Salt
                                    FROM User
                                    WHERE Email_Address=:email
                                 ");
        $statement->bindValue(':email', $username,PDO::PARAM_STR);
	    $statement->execute();
        $pwd_deets = $statement->fetchAll(PDO::FETCH_ASSOC);

        $calculated_hash = hash('sha256', $pwd_deets[0]['Salt'].$password);
	    

        if($calculated_hash == $pwd_deets[0]['Password_Hash']) {
            error_log("User verified");
            return true;
        }
        error_log("User not verified");
        return false;
    }
    public static function get_id_from_username($username) {
        $db = DB::getDB();

        $statement = $db->prepare("SELECT UserID
                                    FROM User
                                    WHERE Email_Address=:email;
                                    ");
        $statement->bindValue(':email',$username,PDO::PARAM_STR);
	    $statement->execute();
        $user_id = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $user_id[0]['UserID'];
    }
}
