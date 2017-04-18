<?php


namespace Wastetopia\Controller;

use Wastetopia\Controller\TokenManager;
use Wastetopia\Model\UserCookieReader;

class Authenticator {
    public static function isAuthenticated() {

        $cookie = new UserCookieReader();

        //extract information from the cookie
        $auth_token = $cookie->get_auth_token();
        $user_id = $cookie->get_user_id();
        error_log("Quick, verify the login");
        //verify
        if(TokenManager::verify($auth_token,$user_id)) {
            error_log("Login verified");
            error_log($auth_token);
            error_log($user_id);
            return true;
        }
        else {
            error_log("Not verified");
            error_log($auth_token);
            error_log($user_id);
            return false;
        }
    }
}
