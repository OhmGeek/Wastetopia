<?php

namespace Wastetopia\Controller;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Wastetopia\Config\CurrentConfig;
use Wastetopia\Controller\TokenManager;
use Wastetopia\Controller\Authenticator;
use Klein\Klein;
use Wastetopia\Model\HeaderInfo;

class LoginController {
    /**
     * Render the login index page
     * @param $response (the Klein response)
     * @param $dest (the destination to forward onto)
     * @param $verificationAlert (defaults to 0, set to 1 if user is verifying account)
     * @return bool|string (return the page or forward if they are already logged in)
     */
    public function index($response, $dest, $verificationAlert = 0) {
        //todo dest parameter with default value
        // this is the static index page (allowing the user to login)
        $loader  = new Twig_Loader_Filesystem(__DIR__.'/../view/');
	    $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('users/login_form.twig');

        // if logged in, don't bother
        if(!Authenticator::isAuthenticated()) {
            return $template->render(array(
                "title" => "Log in",
                "intro" => "Please login to access Wastetopia",
                "dest" => $dest,
                "config" => CurrentConfig::getAll(),
                "header" => HeaderInfo::get(),
		"displayVerificationAlert" => $verificationAlert
            ));
        }
        else {
            // redirect to the base website
            // todo redirect to dest | website base
            $response->redirect($_ENV['ROOT_BASE'] . $dest);
        }
        return "<html><body>You are being redirected<script>window.history.back();</script></body></html>";
    }

    /**
     * API call for logging in
     * @param $username (username of the user)
     * @param $password (password of the user)
     * @param $dest (destination to forward to)
     * @param $response (Klein response object)
     * @return bool|string (Error message or forwarding)
     */
    public function login($username, $password, $dest, $response) {
        //todo dest parameter with default value
        error_log("Enter Error checker");
        // Post destination of the form (params are entered in index.php)
        $outcome = TokenManager::login($username, $password);
        $outcome = json_decode($outcome,true);
        if($outcome['status'] === 'verified') {
            // login success
            error_log($dest);
            // forward the person to the destination/home
            if(isset($dest) || trim($dest) !== "") {
                //forward to the destination uri
                error_log("Try going through destination");
                error_log($dest);
                error_log(json_encode(count_chars($dest)));
                //$response->redirect($dest);
            }
            error_log("Not set. Direct them");
            return "<html><script>window.history.back();</script></html>";
        }


            // incorrect details
            return "INCORRECT DETAILS";
    }

    /**
     * Log the current user out
     * @return string (the logout page)
     */
    public function logout() {
        header("Cache-Control: no-store, must-revalidate, max-age=0");
        setcookie("gpwastetopiadata", null);

        $loader  = new Twig_Loader_Filesystem(__DIR__.'/../view/');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('users/logout.twig');

        return $template->render(array("config" => CurrentConfig::getAll()));
    }
}
