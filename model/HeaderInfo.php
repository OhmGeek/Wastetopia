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

    /**
     * Get the info needed for the header (in the templates)
     * @return array
     */
    public static function get() {
        return array(
            "header" => array(
                "isLoggedIn" => Authenticator::isAuthenticated()
            )
        );
    }
}