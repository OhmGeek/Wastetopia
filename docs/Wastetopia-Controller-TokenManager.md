Wastetopia\Controller\TokenManager
===============






* Class name: TokenManager
* Namespace: Wastetopia\Controller







Methods
-------


### login

    string Wastetopia\Controller\TokenManager::login($username, $password)

Generate a token after authenticating login



* Visibility: **public**
* This method is **static**.


#### Arguments
* $username **mixed** - &lt;p&gt;(username)&lt;/p&gt;
* $password **mixed** - &lt;p&gt;(password)&lt;/p&gt;



### verify

    boolean Wastetopia\Controller\TokenManager::verify($auth_token, $user_id)

Verify whether a token is valid



* Visibility: **public**
* This method is **static**.


#### Arguments
* $auth_token **mixed** - &lt;p&gt;(the auth token)&lt;/p&gt;
* $user_id **mixed** - &lt;p&gt;(the corresponding user id)&lt;/p&gt;


