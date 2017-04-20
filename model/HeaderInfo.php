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

    public static function get() {
        return array(
            "header" => array(
                "isLoggedIn" => Authenticator::isAuthenticated()
            )
        );
    }
}