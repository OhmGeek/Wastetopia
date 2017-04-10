<?php

namespace Wastetopia\Controller;

use Wastetopia\Model\RegistrationModel;
use Twig_Loader_Filesystem;
use Twig_Environment;

use Wastetopia\Config\CurrentConfig;

require("../../public/PHPMailer_5.2.0/class.PHPMailer.php"); //PHPMailer location

class RegistrationController
{

    public function __construct()
    {
        $this->model = new RegistrationModel();

        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);
    }

    /**
     * Generates the HTML for the Registration page
     * @return mixed
     */
    function generatePage()
    {
        $template = $this->twig->loadTemplate('users/registrationForm.twig'); // Need to add Twig file

        $CurrentConfig = new CurrentConfig();
        $config = $CurrentConfig->getAll();

        return $template->render(array("config" => $config)); // Need to pass config stuff
    }


    /** Checks an email address using a Regex (NEEDS TESTING)
     * @param $email
     * @return bool (True if valid email)
     */
    function checkValidEmail($email){
        return (filter_var($email, FILTER_VALIDATE_EMAIL));
    }


    /**
     * Checks if the given email already exists in the database
     * @param $email
     * @return mixed
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
                //$final = True; // For testing
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

    function errorMessage($e){
        $errorArray = array("error" => $e);
        return json_encode($errorArray);
    }

    function successMessage($s){
        $successArray = array("success" => $s);
        return json_encode($successArray);
    }
    
    
    /**
    *NEED TO CHANGE THE LINK FOR PRODUCTION VERSION (use config)
    * Sends an email to the new user with the verification code
    * @param $email
    * @return bool
    */
    function sendVerificationEmail($email, $name){
        $code = $this->model->getVerificationCode($email);
        print_r($code);
        if($code == -1){
            return False;   
        }
        $message = "Your Activation Code is ".$code."";
        $to=$email;
        $subject="Activation Code For Wastetopia";
        $from = 'cs.seg04@durham.ac.uk'; 
        $body='Your Activation Code is '.$code.' Please Click On This link <a href="VERIFICATION_URL">https://wastetopia-pr-27.herokuapp.com/register/verify/'.$code.'</a>to activate  your account.';
        $headers = "From:".$from;
//         print_r($body);
//         return mail($to,$subject,$body,$headers);
    
//         // PHPMailer code
        

        $mail = new PHPMailer();

        $mail->IsSMTP(true);                                      // set mailer to use SMTP
        $mail->Host = "smtp.dur.ac.uk";  // specify main and backup server
        $mail->SMTPAuth = true;     // turn on SMTP authentication
        $mail->Username = "dcs8s04";  // SMTP username
        $mail->Password = "HHrv4673"; // SMTP password

        $mail->From = $from;
        $mail->FromName = "Wastetopia";
        
        $mail->AddAddress($email, $name);
       
        $mail->WordWrap = 50;                                 // set word wrap to 50 characters
        $mail->IsHTML(true);                                  // set email format to HTML

        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = "STILL TO ADD";

        if(!$mail->Send())
        {
           return False;
           exit;
        }

        return True;
      
    }
}
?>
