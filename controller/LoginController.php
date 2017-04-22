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
     * @return bool|string (return the page or forward if they are already logged in)
     */
    public function index($response, $dest) {
        //todo dest parameter with default value
        // this is the static index page (allowing the user to login)
        $loader  = new Twig_Loader_Filesystem(__DIR__.'/../view/');
	    $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('users/login_form.twig');

        // if logged in, don't bother
        if(!Authenticator::isAuthenticated()) {
            return $template->render(array(
                "title" => "Login",
                "intro" => "Please login to access Wastetopia",
                "dest" => $dest,
                "config" => CurrentConfig::getAll(),
                "header" => HeaderInfo::get()
            ));
        }
        else {
            // redirect to the base website
            // todo redirect to dest | website base
            $response->redirect($_ENV['ROOT_BASE'] . $dest);
        }
        //todo return a 'click here to return to the main site page'
        return true; // we can return true otherwise, as we will forward people.
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
        // Post destination of the form (params are entered in index.php)
        $outcome = TokenManager::login($username, $password);
        $outcome = json_decode($outcome,true);
        if($outcome['status'] === 'verified') {
            // login success
            // forward the person to the destination/home
            if(isset($dest) | $dest == "") {
                //forward to the destination uri
                error_log($dest);
                $response->redirect($dest);
                return "Forward";
            }
            error_log("Not set. Direct them");
            $response->redirect($_ENV['ROOT_BASE']);
            return "Normal";
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

        return $template->render(array());
    }
}
