Wastetopia\Model\RegistrationModel
===============

Class RegistrationModel - Used for Registration page




* Class name: RegistrationModel
* Namespace: Wastetopia\Model







Methods
-------


### __construct

    mixed Wastetopia\Model\RegistrationModel::__construct()

RegistrationModel constructor.



* Visibility: **public**




### deleteUserByName

    boolean Wastetopia\Model\RegistrationModel::deleteUserByName($firstName, $lastName)

Deletes any user with the given name. ONLY FOR TESTING. REMOVE LATER.



* Visibility: **public**


#### Arguments
* $firstName **mixed**
* $lastName **mixed**



### deleteUser

    boolean Wastetopia\Model\RegistrationModel::deleteUser($email)

Deletes a user with the given email address



* Visibility: **public**


#### Arguments
* $email **mixed**



### checkEmailExists

    boolean Wastetopia\Model\RegistrationModel::checkEmailExists($email)

Checks whether the given email address is already in the database



* Visibility: **public**


#### Arguments
* $email **mixed**



### addMainUserDetails

    \Wastetopia\Model\The Wastetopia\Model\RegistrationModel::addMainUserDetails($forename, $surname, $email, $passwordHash, $salt, $pictureURL, $verificationCode)

Adds the basic details for a user (name, email, passwordHash, salt)



* Visibility: **public**


#### Arguments
* $forename **mixed**
* $surname **mixed**
* $email **mixed**
* $passwordHash **mixed**
* $salt **mixed** - &lt;p&gt;(for the password)&lt;/p&gt;
* $pictureURL **mixed**
* $verificationCode **mixed**



### addUser

    boolean Wastetopia\Model\RegistrationModel::addUser($forename, $surname, $email, $password, null $pictureURL)

Adds a user and then adds a picture for that user



* Visibility: **public**


#### Arguments
* $forename **mixed**
* $surname **mixed**
* $email **mixed**
* $password **mixed**
* $pictureURL **null**



### getVerificationCode

    mixed Wastetopia\Model\RegistrationModel::getVerificationCode($email)

Returns the verification code to send to the given email



* Visibility: **public**


#### Arguments
* $email **mixed**



### generateSalt

    string Wastetopia\Model\RegistrationModel::generateSalt($min, $max)

Generates a random Salt string (in Hexadecimal) between 30 and 40 bytes in length



* Visibility: **public**


#### Arguments
* $min **mixed** - &lt;p&gt;(default 30)&lt;/p&gt;
* $max **mixed** - &lt;p&gt;(default 40)&lt;/p&gt;



### verifyUser

    boolean Wastetopia\Model\RegistrationModel::verifyUser($verificationCode)

Changes the Active flag for user with the given verification code



* Visibility: **public**


#### Arguments
* $verificationCode **mixed**


