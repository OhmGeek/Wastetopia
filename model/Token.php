<?php
namespace Wastetopia\Model;

class Token {
    private static $before_salt = "Dr.Pr0jectWA5t0Pia";
    private static $after_salt = "EndSalt11!!!1";

    public static function generate_token($user_id) {
        error_log("Before salt: ".self::$before_salt);
        error_log("After salt: ".self::$after_salt);
        error_log("Date: ".date("Y-m-d"));
        error_log("Remote address: ".$_SERVER['REMOTE_ADDR']);
        error_log("Host name: ".gethostname());

        $token_data = self::$before_salt. date("Y-m-d") . $_SERVER['REMOTE_ADDR'] . gethostname() . $user_id . self::$after_salt;
        $token = hash("sha256", $token_data);
        return $token;
    }

    public static function verify_token($auth_token, $user_id) {
        error_log("Auth token: ".$auth_token);
        error_log("User ID: ".$user_id);
        error_log("Generate expected token");
        $expected_token = self::generate_token($user_id);
        error_log("Expected token: ".$expected_token);
	    return ($expected_token == $auth_token);
    }
}
