<?php
namespace Wastetopia\Model;

class Token {
    private static $before_salt = "Dr.Pr0jectWA5t0Pia";
    private static $after_salt = "EndSalt11!!!1";

    /**
     * @param $userID
     * @return string
     */
    public static function generateToken($userID) {
        $tokenData = self::$before_salt. date("Y-m-d") . gethostname() . $userID . self::$after_salt;
        $token = hash("sha256", $tokenData);
        return $token;
    }

    /**
     * @param $authToken
     * @param $userID
     * @return bool
     */
    public static function verifyToken($authToken, $userID) {
        $expectedToken = self::generateToken($userID);
	    return ($expectedToken == $authToken);
    }
}
