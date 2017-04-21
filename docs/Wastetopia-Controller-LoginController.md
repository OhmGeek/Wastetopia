Wastetopia\Controller\LoginController
===============






* Class name: LoginController
* Namespace: Wastetopia\Controller







Methods
-------


### index

    boolean|string Wastetopia\Controller\LoginController::index($response, $dest)

Render the login index page



* Visibility: **public**


#### Arguments
* $response **mixed** - &lt;p&gt;(the Klein response)&lt;/p&gt;
* $dest **mixed** - &lt;p&gt;(the destination to forward onto)&lt;/p&gt;



### login

    boolean|string Wastetopia\Controller\LoginController::login($username, $password, $dest, $response)

API call for logging in



* Visibility: **public**


#### Arguments
* $username **mixed** - &lt;p&gt;(username of the user)&lt;/p&gt;
* $password **mixed** - &lt;p&gt;(password of the user)&lt;/p&gt;
* $dest **mixed** - &lt;p&gt;(destination to forward to)&lt;/p&gt;
* $response **mixed** - &lt;p&gt;(Klein response object)&lt;/p&gt;



### logout

    string Wastetopia\Controller\LoginController::logout()

Log the current user out



* Visibility: **public**



