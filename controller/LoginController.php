<?php

namespace Wastetopia\Controller;

use Wastetopia\Controller\TokenManager;
use Wastetopia\Controller\Authenticator;

class Login_Controller {
    public static function index() {
        //todo dest parameter with default value
        // this is the static index page (allowing the user to login)
        $loader  = new Twig_Loader_Filesystem(__DIR__.'/../../view/login');
	$twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('login_form.html');

        // if logged in, don't bother
        if(!Authenticator::isAuthenticated()) {
            return $template->render(array());
        }
        else {
            header('Location: community.dur.ac.uk/cs.seg4/Wastetopia/Wastetopia/');
        }
    }

    public static function login($username, $password, $dest) {
        //todo dest parameter with default value
        // Post destination of the form (params are entered in index.php)
        $outcome = TokenController::login($_POST['username'], $_POST['password']);
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
            header('Location: community.dur.ac.uk/cs.seg4/Wastetopia/Wastetopia/');
        }
        else {
            // incorrect details


        }
    }
}
