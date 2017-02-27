<?php


namespace Wastetopia\Controller;

use Wastetopia\Model\User;
use Wastetopia\Model\Token;
use Wastetopia\Model\UserCookieWriter;

class TokenManager {
    
    public static function login($username, $password) {
        if(User::verify_credentials($username,$password)) {

            // get the user id
            $user_id = User::get_id_from_username($username);

            // generate the token using our token generator
            $token = Token::generate_token($user_id);

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
    public static function verify($auth_token,$user_id) {
            //return true if token is correct, false if not correct
            return Token::verify_token($auth_token,$user_id);
    }
}
