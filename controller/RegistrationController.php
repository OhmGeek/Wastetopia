<?php

//TODO: Fix issue with not being able to find PHPMailerAutoload.php
namespace Wastetopia\Controller;

use Wastetopia\Model\HeaderInfo;
use Wastetopia\Model\RegistrationModel;
use Twig_Loader_Filesystem;
use Twig_Environment;

use Wastetopia\Config\CurrentConfig;


/**
 * Class RegistrationController - Used to generate Registration page and handle all of its inputs
 * @package Wastetopia\Controller
 */
class RegistrationController
{

    /**
     * RegistrationController constructor.
     */
    public function __construct()
    {
        $this->model = new RegistrationModel();

        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);
    }

	
	function generateVerificationPage(){
		$template = $this->twig->loadTemplate("users/verificationPage.twig");
		return $template->render(array("header"=>HeaderInfo::get()));
	}
	
    /**
     * Generates the HTML for the Registration page
     * @return HTML
     */
    function generatePage()
    {
        $template = $this->twig->loadTemplate('users/registrationForm.twig'); // Need to add Twig file

        $CurrentConfig = new CurrentConfig();
        $config = $CurrentConfig->getAll();

        return $template->render(array(
            "config" => $config,
            "header" => HeaderInfo::get()
        )); // Need to pass config stuff
    }


    /** Checks an email address using a Regex
     * @param $email
     * @return bool (True if valid email)
     */
    function checkValidEmail($email){
        return (filter_var($email, FILTER_VALIDATE_EMAIL));
    }


    /**
     * Checks if the given email already exists in the database
     * @param $email
     * @return bool
     */
    function checkAvailable($email){
        $temp = $this->model->checkEmailExists($email);

        // $temp will be True if the email exists, want to then return False
        return !$temp;
    }


    /**
     * Checks the two given passwords match
     * @param $pwd
     * @param $pwdConfirm
     * @return bool
     */
    function checkPassword($pwd, $pwdConfirm){
        if($pwd == $pwdConfirm){
            return True;
        }
        return False;
    }

    /**
     * Main function to add user (performs all the checks)
     * @param $forename
     * @param $surname
     * @param $email
     * @param $password
     * @param $passwordConfirm
     * @param null $pictureURL (If not given, will save default image)
     * @return JSON (in form {"error":...} or {"success":...}
     */
    function addUser($forename, $surname, $email, $password, $passwordConfirm, $pictureURL = NULL){
        // Check password length
        if(strlen($password) < 8){
            return $this->errorMessage("Password must be at least 8 characters in length");   
        }
        
        // Check passwords match
        if(!($this->checkPassword($password, $passwordConfirm))){
            return $this->errorMessage("Passwords don't match");
        }
        // Check the email is of a valid type
        elseif(!$this->checkValidEmail($email)){
            return $this->errorMessage("Email is not valid");
        }
        // check email is not already in use
        elseif(!$this->checkAvailable($email)){
            return $this->errorMessage("Email already in use");
        }
        else {
            // Add user to DB
            $result = $this->model->addUser($forename, $surname, $email, $password, $pictureURL);
           
            // Send back success or error message
            if (!($result)) {
                return $this->errorMessage("Couldn't add user (something unexpected went wrong)");
            } else {
                // Send verification email
                $name = $forename." ".$surname;
                $final = $this->sendVerificationEmail($email, $name);
                if (!($final)){
                    // Delete user so they can try again
                    $this->model->deleteUser($email);
                    return $this->errorMessage("Couldn't send verification email to that address, please use a different email");
                    
                }else{
                    return $this->successMessage("Success");
                }      
            }
        }
    }

    /**
     * JSON error message
     * @param $e
     * @return string
     */
    function errorMessage($e){
        $errorArray = array("error" => $e);
        return json_encode($errorArray);
    }

    /**
     * JSON success message
     * @param $s
     * @return string
     */
    function successMessage($s){
        $successArray = array("success" => $s);
        return json_encode($successArray);
    }
    
    
    /**
    * Sends an email to the new user with the verification code
    * @param $email
     * @param $reactivation (defaults to 0. 1 if this is sent as a result of user changing their email)
    * @return bool
    */
    function sendVerificationEmail($email, $name, $reactivation = 0){
	    error_log("Email: ".$email);
	    error_log("Name: ".$name);
        $CurrentConfig = new CurrentConfig();
        $config = $CurrentConfig->getAll();
            
        $code = $this->model->getVerificationCode($email);
        if($code == -1){
            return False;   
        }
        
        $root = $config["ROOT_BASE"]; // Base url for the website
        $fullURL = $root."/register/verify/".$code; // Verification URL
        
        $message = "Your Activation Code is ".$code."";
        $to=$email;
        $subject="Activation Code For Wastetopia";
        $from = $config["EMAIL_ADDRESS"];
        $body = "";
        $altBody = "";
        // If reactivating new account, let user know why they have to do this again
        if ($reactivation){
            $body = '<p> This has been sent as a result of you changing your email. You must now reactivate your account. </p> <br>';
            $altBody = "This has been sent as a result of you changing your email. You must now reactivate your account.";
        }
        $body.='<p>Your Activation Code is '.$code.'. </p> <br> <p> Please Click On This link: </p> <a href='.$fullURL.'> https:'.$fullURL.' </a> <br> <p> to activate  your account. </p>';
        $altBody .= "Please go to: https:".$fullURL;

        return $this->sendEmail($from, $subject, $body, $altBody, $email, $name);
    }
    
    /**
    * Main function to send an email
    * @param $from
    * @param $subject
    * @param $body
    * @param $altBody
    * @param $email
    * @param $name
    * @return bool
    */
    function sendEmail($from, $subject, $body, $altBody, $email, $name){	 
	    error_log("From: ".$from);
	    error_log("Subject: ".$subject);
	    error_log("Body: ".$body);
	    error_log("Email: ".$email);
	    error_log("Name: ".$name);
	    
	    $CurrentConfig = new CurrentConfig();
        $config = $CurrentConfig->getAll();
	    
	    // PHPMailer code
	    $mail = new \PHPMailer(true); //true makes it give errors
        $mail->IsSMTP();                           // set mailer to use SMTP
        $mail->Host = $config["EMAIL_HOST"];
        $mail->Port = $config["EMAIL_PORT"]; //25 for non-SSL, 465  for SSL, 587 for tls
        
        $mail->SMTPSecure = $config["EMAIL_SECURITY"];  // ssl or tls
  
        $mail->SMTPAuth = true;     // turn on SMTP authentiocation

        //$mail->SMTPDebug = 2; // Print debug log for testing

        $mail->Username = $config["EMAIL_ADDRESS"];  // SMTP username
        $mail->Password = $config["EMAIL_PASSWORD"]; // SMTP password
        $mail->From = $config["EMAIL_ADDRESS"];
        $mail->FromName = "Wastetopia";
        
        $mail->AddAddress($email, $name);
       
        $mail->WordWrap = 50;               // set word wrap to 50 characters
        $mail->IsHTML(true);                // set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody;          // altBody if email doesn't use HTML
        if(!$mail->Send())
        {
           return False;
        }
        return True;    
    }
}
?>
