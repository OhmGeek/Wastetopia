<?php
namespace Wastetopia\Model;

class Token {
    private $before_salt = "Dr.Pr0jectWA5t0Pia";
    private $after_salt = "EndSalt11!!!1";

    public static function generate_token($user_id) {
        $token_data = $before_salt. date("Y-m-d") . $_SERVER['REMOTE_ADDR'] . gethostname() . $user_id . $after_salt;
        $token = hash("sha256", $token_data);
        return $token;
    }

    public static function verify_token($auth_token, $user_id) {
        $expected_token = self::generate_token($user_id);
	    return ($expected_token == $auth_token);
    }
}
