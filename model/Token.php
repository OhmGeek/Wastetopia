<?php
namespace Wastetopia\Model;

class Token {
    private static $before_salt = "Dr.Pr0jectWA5t0Pia";
    private static $after_salt = "EndSalt11!!!1";

    public static function generate_token($user_id) {
        $token_data = self::$before_salt. date("Y-m-d") . gethostname() . $user_id . self::$after_salt;
        $token = hash("sha256", $token_data);
        return $token;
    }

    public static function verify_token($auth_token, $user_id) {
        $expected_token = self::generate_token($user_id);
	    return ($expected_token == $auth_token);
    }
}
