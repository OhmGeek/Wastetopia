<?php
namespace  Wastetopia\Model;

class UserCookieReader {
    public function __construct() {
        // load in the cookie
        $this->user_cookie = $_COOKIE['gpwastetopiadata'];

        // remove the slashes
        $this->user_cookie = stripslashes($this->user_cookie);

        // create an array from json (true denotes array)
        $this->user_cookie = json_decode($this->user_cookie, true);
    }
    public function get_auth_token() {
        return $this->user_cookie['auth_token'];
    }
    public function get_user_id() {
        return $this->user_cookie['user_id'];
    }

}