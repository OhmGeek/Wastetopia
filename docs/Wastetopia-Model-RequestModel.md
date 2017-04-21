Wastetopia\Model\RequestModel
===============






* Class name: RequestModel
* Namespace: Wastetopia\Model







Methods
-------


### __construct

    mixed Wastetopia\Model\RequestModel::__construct()





* Visibility: **public**




### getUserID

    string Wastetopia\Model\RequestModel::getUserID()

Returns the ID of the user currently logged in



* Visibility: **public**




### getTransactionIDFromListingID

    integer Wastetopia\Model\RequestModel::getTransactionIDFromListingID($listingID)

Returns the ID of the last incomplete transaction the $userID made for the $listingID



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### getLastTransactionID

    integer Wastetopia\Model\RequestModel::getLastTransactionID($userID)

Returns the ID of the last transaction the User made



* Visibility: **private**


#### Arguments
* $userID **mixed**



### getLastItemID

    integer Wastetopia\Model\RequestModel::getLastItemID($name, $useBy, $description)

Returns the ID of the last item inserted with the given parameters



* Visibility: **private**


#### Arguments
* $name **mixed**
* $useBy **mixed**
* $description **mixed**



### getLastListingID

    integer Wastetopia\Model\RequestModel::getLastListingID($itemID, $locationID, $userID)

Returns the ID of the last item inserted with the given parameters



* Visibility: **private**


#### Arguments
* $itemID **mixed**
* $locationID **mixed**
* $userID **mixed**



### getRequestQuantity

    integer Wastetopia\Model\RequestModel::getRequestQuantity($listing_id, $transaction_id)

Returns the quantity of the item requested



* Visibility: **public**


#### Arguments
* $listing_id **mixed**
* $transaction_id **mixed**



### requestItem

    boolean Wastetopia\Model\RequestModel::requestItem($listing_id, $quantity)

Makes a new request for the specified quantity of the specified item



* Visibility: **public**


#### Arguments
* $listing_id **mixed**
* $quantity **mixed**



### confirmRequest

    boolean Wastetopia\Model\RequestModel::confirmRequest($listing_id, $transaction_id, $quantity)

Acepts the request, optionally changing the quantity given away



* Visibility: **public**


#### Arguments
* $listing_id **mixed**
* $transaction_id **mixed**
* $quantity **mixed**



### getRequestIDPairsForUser

    \Wastetopia\Model\associative Wastetopia\Model\RequestModel::getRequestIDPairsForUser($unviewed_only, $listing_id)

Returns all unique identifiers for the requests associated with the user, optionally only the unviewed requests
optionally specify a specific listing to get requests for
optionally specify unviewed_only for whether to get all regardless of whether they've been viewed(0)
only the unviewed requests(1)
only the viewed requests(2)



* Visibility: **public**


#### Arguments
* $unviewed_only **mixed**
* $listing_id **mixed**



### renewListing

    boolean Wastetopia\Model\RequestModel::renewListing($listing_id, $new_quantity, $new_use_by_date)

Change the quantity of the item being offered



* Visibility: **public**


#### Arguments
* $listing_id **mixed**
* $new_quantity **mixed**
* $new_use_by_date **mixed**



### withdrawListing

    boolean Wastetopia\Model\RequestModel::withdrawListing($listing_id)

'Deletes' the listing



* Visibility: **public**


#### Arguments
* $listing_id **mixed**



### getPendingTransactionsForListing

    array Wastetopia\Model\RequestModel::getPendingTransactionsForListing($listing_id)

Gets all the pending transactions for a given listing



* Visibility: **public**


#### Arguments
* $listing_id **mixed**



### rejectRequest

    void Wastetopia\Model\RequestModel::rejectRequest($listing_id, $transaction_id)

Reject some request for an item



* Visibility: **public**


#### Arguments
* $listing_id **mixed**
* $transaction_id **mixed**



### withdrawRequest

    mixed Wastetopia\Model\RequestModel::withdrawRequest($transaction_id)





* Visibility: **public**


#### Arguments
* $transaction_id **mixed**



### migratePendingTransactions

    boolean Wastetopia\Model\RequestModel::migratePendingTransactions($old_listing_id, $new_listing_id)

Links all the pending transactions for the old listing to the new listing



* Visibility: **public**


#### Arguments
* $old_listing_id **mixed**
* $new_listing_id **mixed**


