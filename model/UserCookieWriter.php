<?php

namespace Wastetopia\Model;

class UserCookieWriter {
    private $TIME_OFFSET = 60000;

    /**
     * UserCookieWriter constructor.
     */
    public function __construct() {
        $this->cookie_data = array();
        $this->finalised = false;
    }

    /**
     * Set the auth token in the cookie
     * @param $auth_token
     */
    public function set_auth_token($auth_token) {
        $this->cookie_data['auth_token'] = $auth_token;
    }

    /**
     * Set the userid in the cookie
     * @param $user_id
     */
    public function set_user_id($user_id) {
        $this->cookie_data['user_id'] = $user_id;
    }

    /**
     * Write to the cookie
     */
    public function write() {
        if(!$this->finalised) {
            $json_data = json_encode($this->cookie_data);
            error_log("Writing cookie now");
            error_log($json_data);
            setcookie("gpwastetopiadata", $json_data, time() + $this->TIME_OFFSET,"/");
        }
    }

}