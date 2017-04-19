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

        //verify
        if(TokenManager::verify($auth_token,$user_id)) {
            error_log("Login verified");
            return true;
        }
        else {
            error_log("Not verified");
            return false;
        }
    }
}
