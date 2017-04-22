Wastetopia\Controller\RegistrationController
===============

Class RegistrationController - Used to generate Registration page and handle all of its inputs




* Class name: RegistrationController
* Namespace: Wastetopia\Controller







Methods
-------


### __construct

    mixed Wastetopia\Controller\RegistrationController::__construct()

RegistrationController constructor.



* Visibility: **public**




### generatePage

    \Wastetopia\Controller\HTML Wastetopia\Controller\RegistrationController::generatePage()

Generates the HTML for the Registration page



* Visibility: **public**




### checkValidEmail

    boolean Wastetopia\Controller\RegistrationController::checkValidEmail($email)

Checks an email address using a Regex



* Visibility: **public**


#### Arguments
* $email **mixed**



### checkAvailable

    boolean Wastetopia\Controller\RegistrationController::checkAvailable($email)

Checks if the given email already exists in the database



* Visibility: **public**


#### Arguments
* $email **mixed**



### checkPassword

    boolean Wastetopia\Controller\RegistrationController::checkPassword($pwd, $pwdConfirm)

Checks the two given passwords match



* Visibility: **public**


#### Arguments
* $pwd **mixed**
* $pwdConfirm **mixed**



### addUser

    \Wastetopia\Controller\JSON Wastetopia\Controller\RegistrationController::addUser($forename, $surname, $email, $password, $passwordConfirm, null $pictureURL)

Main function to add user (performs all the checks)



* Visibility: **public**


#### Arguments
* $forename **mixed**
* $surname **mixed**
* $email **mixed**
* $password **mixed**
* $passwordConfirm **mixed**
* $pictureURL **null** - &lt;p&gt;(If not given, will save default image)&lt;/p&gt;



### errorMessage

    string Wastetopia\Controller\RegistrationController::errorMessage($e)

JSON error message



* Visibility: **public**


#### Arguments
* $e **mixed**



### successMessage

    string Wastetopia\Controller\RegistrationController::successMessage($s)

JSON success message



* Visibility: **public**


#### Arguments
* $s **mixed**



### sendVerificationEmail

    boolean Wastetopia\Controller\RegistrationController::sendVerificationEmail($email, $name, $reactivation)

Sends an email to the new user with the verification code



* Visibility: **public**


#### Arguments
* $email **mixed**
* $name **mixed**
* $reactivation **mixed** - &lt;p&gt;(defaults to 0. 1 if this is sent as a result of user changing their email)&lt;/p&gt;



### sendEmail

    boolean Wastetopia\Controller\RegistrationController::sendEmail($from, $subject, $body, $altBody, $email, $name)

Main function to send an email



* Visibility: **public**


#### Arguments
* $from **mixed**
* $subject **mixed**
* $body **mixed**
* $altBody **mixed**
* $email **mixed**
* $name **mixed**


