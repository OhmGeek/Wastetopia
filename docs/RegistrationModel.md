RegistrationModel
===============

Created by PhpStorm.

User: Stephen
Date: 03/03/2017
Time: 10:06


* Class name: RegistrationModel
* Namespace: 







Methods
-------


### __construct

    mixed RegistrationModel::__construct()

RegistrationModel constructor.



* Visibility: **public**




### getLastInsertID

    mixed RegistrationModel::getLastInsertID()





* Visibility: **private**




### checkEmailExists

    boolean RegistrationModel::checkEmailExists($email)

Checks whether the given email address is already in the database



* Visibility: **public**


#### Arguments
* $email **mixed**



### addMainUserDetails

    \The RegistrationModel::addMainUserDetails($forename, $surname, $email, $passwordHash, $salt, $pictureURL)

Adds the basic details for a user (name, email, passwordHash, salt)



* Visibility: **public**


#### Arguments
* $forename **mixed**
* $surname **mixed**
* $email **mixed**
* $passwordHash **mixed**
* $salt **mixed** - &lt;p&gt;(for the password)&lt;/p&gt;
* $pictureURL **mixed**



### addUser

    boolean RegistrationModel::addUser($forename, $surname, $email, $password, null $pictureURL)

Adds a user and then adds a picture for that user



* Visibility: **public**


#### Arguments
* $forename **mixed**
* $surname **mixed**
* $email **mixed**
* $password **mixed**
* $pictureURL **null**


