<?php

class UserCookieWriter {
    private $TIME_OFFSET = 60000;
    public function __construct() {
        $this->cookie_data = array();
        $this->finalised = false;
    }
    public function set_auth_token($auth_token) {
        $this->cookie_data['auth_token'] = $auth_token;
    }
    public function set_user_id($user_id) {
        $this->cookie_data['user_id'] = $user_id;
    }
    public function write() {
        if(!$this->finalised) {
            $json_data = json_encode($this->cookie_data);
            setcookie("gpwastetopiadata", $json_data, time() + $this->TIME_OFFSET);
        }
    }

}