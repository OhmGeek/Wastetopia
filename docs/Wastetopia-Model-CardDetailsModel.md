Wastetopia\Model\CardDetailsModel
===============

Class CardDetailsModel - Used to get Display information for the Cards




* Class name: CardDetailsModel
* Namespace: Wastetopia\Model







Methods
-------


### __construct

    mixed Wastetopia\Model\CardDetailsModel::__construct()

CardDetailsModel constructor.



* Visibility: **public**




### getUserID

    integer Wastetopia\Model\CardDetailsModel::getUserID()

Gets current user who's logged in



* Visibility: **public**




### getUserDetails

    mixed Wastetopia\Model\CardDetailsModel::getUserDetails($userID)

Gets all details from the User table for the given user



* Visibility: **public**


#### Arguments
* $userID **mixed**



### getUserImage

    \Wastetopia\Model\URL Wastetopia\Model\CardDetailsModel::getUserImage($userID)

Gets the profile picture of the given user



* Visibility: **public**


#### Arguments
* $userID **mixed**



### getCardDetails

    mixed Wastetopia\Model\CardDetailsModel::getCardDetails($listingID)

Returns the details needed for display on the profile page given the listing ID



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### getDefaultImage

    String Wastetopia\Model\CardDetailsModel::getDefaultImage($listingID)

Returns the default image for this listing (if there is one)



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### isRequesting

    boolean Wastetopia\Model\CardDetailsModel::isRequesting($listingID, $userID)

Checks whether the given user has an ongoing request for the given listing



* Visibility: **public**


#### Arguments
* $listingID **mixed**
* $userID **mixed**



### isWatching

    boolean Wastetopia\Model\CardDetailsModel::isWatching($listingID, $userID)

Checks whether the given user has the listing in their watch list



* Visibility: **public**


#### Arguments
* $listingID **mixed**
* $userID **mixed**


