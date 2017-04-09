<?php

namespace Wastetopia\Controller;

use Wastetopia\Model\RegistrationModel;
use Twig_Loader_Filesystem;
use Twig_Environment;

class RegistrationController
{
	
    public function __construct()
    {
        $this->model = new RegistrationModel();
		
        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);
    }
	function generatePage()
	{
		$template = $this->twig->loadTemplate('users/registration.html');
        return $template->render(array());
	}
	function checkValid($email){
		$temp = $this->model->checkEmailExists($email);
		return $temp;
	}
	function checkPassword($pwd,$pwdConfirm){
		if($pwd == $pwdConfirm){
			return True;
		}
		return False;
	}
	function addUser($forename, $surname, $email, $password,$passwordConfirm, $pictureURL = NULL){
		if(checkValid($email) == False and checkPassword($password,$passwordConfirm) == True){
			return False;
		}
		else{
			$this->model->addUser($forename, $surname, $email, $password, $pictureURL);
		}
		return True;
	}
}
?>