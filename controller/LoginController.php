<?php

namespace Wastetopia\Controller;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Wastetopia\Controller\TokenManager;
use Wastetopia\Controller\Authenticator;
use Klein\Klein;
class LoginController {
    public function index($response) {
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
            ));
        }
        else {
            // redirect to the base website
            // todo redirect to dest | website base
            $response->redirect($_ENV['ROOT_BASE']);
        }
        //todo return a 'click here to return to the main site page'
        return true; // we can return true otherwise, as we will forward people.
    }

    public function login($username, $password, $dest, $response) {
        //todo dest parameter with default value
        // Post destination of the form (params are entered in index.php)
        $outcome = TokenManager::login($username, $password);
        $outcome = json_decode($outcome,true);
        if($outcome['status'] === 'verified') {
            // login success
            // forward the person to the destination/home
            if(isset($dest)) {
                //forward to the destination uri
                header('Location: $dest');
                exit();
            }
            //forward home
            $response->redirect($_ENV['ROOT_BASE']);
        }
        else {
            // incorrect details
            return "INCORRECT DETAILS";

        }
        return true; // later return a page that has a link to the main site.
    }
}
