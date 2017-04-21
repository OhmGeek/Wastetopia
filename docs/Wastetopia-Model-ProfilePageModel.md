Wastetopia\Model\ProfilePageModel
===============

Class ProfilePageModel - Functions for anything on profile page




* Class name: ProfilePageModel
* Namespace: Wastetopia\Model







Methods
-------


### __construct

    mixed Wastetopia\Model\ProfilePageModel::__construct(\Wastetopia\Model\@userID $userID)

ProfilePageModel constructor.



* Visibility: **public**


#### Arguments
* $userID **Wastetopia\Model\@userID** - &lt;p&gt;ID of user whose profile you&#039;re trying to view&lt;/p&gt;



### getUserID

    integer Wastetopia\Model\ProfilePageModel::getUserID()

Returns the ID of the user whose profile you're trying to view



* Visibility: **private**




### getUserListings

    mixed Wastetopia\Model\ProfilePageModel::getUserListings()

Gets all the listings the current user has put up
Can then use getStateOfListingTransactions() to check if the state of the transactions for those listings



* Visibility: **public**




### getUserReceivingListings

    mixed Wastetopia\Model\ProfilePageModel::getUserReceivingListings()

Gets all the listings the current user is involved in transactions with (where they are the receiver)
Also gets the transactionID of those transactions



* Visibility: **public**




### getUnseenPendingTransactions

    mixed Wastetopia\Model\ProfilePageModel::getUnseenPendingTransactions()

Gets the pending offers made for user's items which they haven't seen yet (notification)



* Visibility: **public**




### getNumberOfUnseenPendingTransactions

    mixed Wastetopia\Model\ProfilePageModel::getNumberOfUnseenPendingTransactions()

Gets the total number of pending offers made for user's items which they haven't seen yet (notification)



* Visibility: **public**




### getStateOfListingTransaction

    mixed Wastetopia\Model\ProfilePageModel::getStateOfListingTransaction($listingID)

Gets all information about transactions for this listing.

If there are no results, this item has not been requested
Each transaction will have an ID and a success flag

* Visibility: **public**


#### Arguments
* $listingID **mixed**



### getDetailsFromTransactionID

    mixed Wastetopia\Model\ProfilePageModel::getDetailsFromTransactionID($transactionID)

Gets the name of the user requesting the transaction, and the quantity involved



* Visibility: **public**


#### Arguments
* $transactionID **mixed**



### setViewed

    void Wastetopia\Model\ProfilePageModel::setViewed($listing_id, $transaction_id, $new_value)

Set the request as having been viewed



* Visibility: **public**


#### Arguments
* $listing_id **mixed**
* $transaction_id **mixed**
* $new_value **mixed**



### getWatchedListings

    mixed Wastetopia\Model\ProfilePageModel::getWatchedListings($userID)

Gets all the listings the current user is watching
Can then use getStateOfListingTransactions() to check if the transaction should go in History or Currently Watching



* Visibility: **public**


#### Arguments
* $userID **mixed**



### deleteFromWatchList

    boolean Wastetopia\Model\ProfilePageModel::deleteFromWatchList($listingID, $userID)

Deletes a listing from user's watch list



* Visibility: **public**


#### Arguments
* $listingID **mixed**
* $userID **mixed**



### addToWatchList

    boolean Wastetopia\Model\ProfilePageModel::addToWatchList($listingID, $userID)

Adds a listing to a user's watch list



* Visibility: **public**


#### Arguments
* $listingID **mixed**
* $userID **mixed**



### isRequesting

    boolean Wastetopia\Model\ProfilePageModel::isRequesting($listingID, $userID)

Checks whether the given user has an ongoing request for the given listing



* Visibility: **public**


#### Arguments
* $listingID **mixed**
* $userID **mixed**



### hasRated

    integer Wastetopia\Model\ProfilePageModel::hasRated($transactionID, $userID)

Returns 1 if the given user has rated the given transaction



* Visibility: **public**


#### Arguments
* $transactionID **mixed**
* $userID **mixed**



### setListingTransactionHiddenFlag

    boolean Wastetopia\Model\ProfilePageModel::setListingTransactionHiddenFlag($giverOrReceiver, $transactionID, $value)

Sets the Giver_Viewed or Receiver_Viewed flag to the given value for the given transactionID



* Visibility: **public**


#### Arguments
* $giverOrReceiver **mixed**
* $transactionID **mixed**
* $value **mixed**



### getPasswordDetails

    array Wastetopia\Model\ProfilePageModel::getPasswordDetails($userID)

Gets the password details for the given user)



* Visibility: **public**


#### Arguments
* $userID **mixed**



### updatePassword

    boolean Wastetopia\Model\ProfilePageModel::updatePassword($newPassword)

Hashes new password and stores it in the DB for the current user



* Visibility: **public**


#### Arguments
* $newPassword **mixed**



### getUserEmail

    string Wastetopia\Model\ProfilePageModel::getUserEmail($userID)

Gets the email of the given user



* Visibility: **public**


#### Arguments
* $userID **mixed**



### generateSalt

    string Wastetopia\Model\ProfilePageModel::generateSalt($min, $max)

Generates a random Salt string (in Hexadecimal) between 30 and 40 bytes in length



* Visibility: **public**


#### Arguments
* $min **mixed** - &lt;p&gt;(default 30)&lt;/p&gt;
* $max **mixed** - &lt;p&gt;(default 40)&lt;/p&gt;



### resetAccount

    boolean Wastetopia\Model\ProfilePageModel::resetAccount($userID, $email)

Resets the verification code and active flag for the given user



* Visibility: **public**


#### Arguments
* $userID **mixed**
* $email **mixed**



### changeProfilePicture

    boolean Wastetopia\Model\ProfilePageModel::changeProfilePicture($url)

Replaces the user's profile picture with that at the given URL



* Visibility: **public**


#### Arguments
* $url **mixed**


