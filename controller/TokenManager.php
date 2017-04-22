<?php


namespace Wastetopia\Controller;

use Wastetopia\Model\User;
use Wastetopia\Model\Token;
use Wastetopia\Model\UserCookieWriter;

class TokenManager {

    /**
     * Generate a token after authenticating login
     * @param $username (username)
     * @param $password (password)
     * @return string (JSON response of success)
     */
    public static function login($username, $password) {
        if(User::verifyCredentials($username,$password)) {

            // get the user id
            $user_id = User::getIDFromUsername($username);

            error_log("Logging in");
            error_log("User ID: ".$user_id);
            error_log("Generating token");

            // generate the token using our token generator
            $token = Token::generateToken($user_id);

            error_log("Token: ".$token);

            // now write user_id and token itself to a cookie
            $cookie = new UserCookieWriter();
            $cookie->set_user_id($user_id);
            $cookie->set_auth_token($token);
            $cookie->write();

            // return to the user a status code (useful for API)
            return '{"status": "verified"}';
        }
        else {
            // not logged in
            // return to the user an error
            return '{"error": "invalid credentials"}';
        }
    }

    /**
     * Verify whether a token is valid
     * @param $auth_token (the auth token)
     * @param $user_id (the corresponding user id)
     * @return bool (True => Verified, False => not valid)
     */
    public static function verify($auth_token, $user_id) {
            //return true if token is correct, false if not correct
            return Token::verifyToken($auth_token,$user_id);
    }
}
