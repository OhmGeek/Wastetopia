Wastetopia\Model\PopularityModel
===============

Class PopularityModel - Used when a user rates a transaction




* Class name: PopularityModel
* Namespace: Wastetopia\Model







Methods
-------


### __construct

    mixed Wastetopia\Model\PopularityModel::__construct()

PopularityModel constructor.



* Visibility: **public**




### getUserID

    string Wastetopia\Model\PopularityModel::getUserID()

Returns the ID of the user currently logged in



* Visibility: **public**




### getUserRatingDetails

    array Wastetopia\Model\PopularityModel::getUserRatingDetails($userID)

Gets the number of ratings and average rating of the given user



* Visibility: **public**


#### Arguments
* $userID **mixed**



### setUserRating

    boolean Wastetopia\Model\PopularityModel::setUserRating($userID, $meanRating, $numberOfRatings)

Sets new values for ratings for a given user



* Visibility: **public**


#### Arguments
* $userID **mixed**
* $meanRating **mixed**
* $numberOfRatings **mixed**



### setListingTransactionRated

    boolean Wastetopia\Model\PopularityModel::setListingTransactionRated($transactionID)

Sets the Rated flag to 1 for the given transaction



* Visibility: **public**


#### Arguments
* $transactionID **mixed**



### getUserIDFromTransactionID

    integer Wastetopia\Model\PopularityModel::getUserIDFromTransactionID($transactionID)

Gets the UserID of the User who put up the listing involved in the transaction

@param $transactionID

* Visibility: **public**


#### Arguments
* $transactionID **mixed**



### addNewRating

    boolean Wastetopia\Model\PopularityModel::addNewRating($userID, $rating)

Calculates and adds a new rating for a given user



* Visibility: **public**


#### Arguments
* $userID **mixed**
* $rating **mixed**



### rateTransaction

    boolean Wastetopia\Model\PopularityModel::rateTransaction($transactionID, $rating)





* Visibility: **public**


#### Arguments
* $transactionID **mixed**
* $rating **mixed**


