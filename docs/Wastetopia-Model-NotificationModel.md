Wastetopia\Model\NotificationModel
===============

Class NotificationModel - Used for any notifications (Currently unread messages and unseen requests)




* Class name: NotificationModel
* Namespace: Wastetopia\Model







Methods
-------


### __construct

    mixed Wastetopia\Model\NotificationModel::__construct()

NotificationModel constructor.



* Visibility: **public**




### getUserID

    string Wastetopia\Model\NotificationModel::getUserID()

Returns the ID of the user currently logged in



* Visibility: **private**




### requestNotifications

    integer Wastetopia\Model\NotificationModel::requestNotifications()

Returns the number of unseen transactions (requests) this user has on their listings



* Visibility: **public**




### messageNotifications

    integer Wastetopia\Model\NotificationModel::messageNotifications()

Returns the total number of unread messages this user has



* Visibility: **public**




### getAll

    array Wastetopia\Model\NotificationModel::getAll($json)

Returns array with all notifications in



* Visibility: **public**


#### Arguments
* $json **mixed** - &lt;p&gt;(defaults to 0, set to 1 if you need the array in JSON format)&lt;/p&gt;


