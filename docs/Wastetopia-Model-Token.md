Wastetopia\Model\Token
===============






* Class name: Token
* Namespace: Wastetopia\Model





Properties
----------


### $before_salt

    private mixed $before_salt = "Dr.Pr0jectWA5t0Pia"





* Visibility: **private**
* This property is **static**.


### $after_salt

    private mixed $after_salt = "EndSalt11!!!1"





* Visibility: **private**
* This property is **static**.


Methods
-------


### generateToken

    string Wastetopia\Model\Token::generateToken($userID)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $userID **mixed**



### verifyToken

    boolean Wastetopia\Model\Token::verifyToken($authToken, $userID)





* Visibility: **public**
* This method is **static**.


#### Arguments
* $authToken **mixed**
* $userID **mixed**


