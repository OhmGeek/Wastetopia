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
    private static $loginDest;
    /**
     * Get the info needed for the header (in the templates)
     * @return array
     */
    public static function get() {
        return array(
                "isLoggedIn" => Authenticator::isAuthenticated(),
                "loginLink" => self::$loginDest
        );
    }

    public static function setLoginDest($dest) {
        self::$loginDest = $dest;
    }

}