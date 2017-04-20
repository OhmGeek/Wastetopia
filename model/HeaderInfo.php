<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 20/04/17
 * Time: 23:23
 */

namespace Wastetopia\Model;


use Wastetopia\Controller\Authenticator;

class HeaderInfo
{

    private static function getUserID() {
        $userReader = new UserCookieReader();
        return $userReader->get_user_id();
    }
    public static function get() {
        return array(
            "header" => array(
                "isLoggedIn" => Authenticator::isAuthenticated()
            )
        );
    }
}