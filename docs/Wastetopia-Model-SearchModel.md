Wastetopia\Model\SearchModel
===============






* Class name: SearchModel
* Namespace: Wastetopia\Model







Methods
-------


### __construct

    mixed Wastetopia\Model\SearchModel::__construct()

SearchModel constructor.



* Visibility: **public**




### getCardDetails

    mixed Wastetopia\Model\SearchModel::getCardDetails($listingID)

Returns the details needed for display on the search page given the listing ID



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### getDefaultImage

    mixed Wastetopia\Model\SearchModel::getDefaultImage($listingID)





* Visibility: **public**


#### Arguments
* $listingID **mixed**



### getSearchResults

    mixed Wastetopia\Model\SearchModel::getSearchResults($userLat, $userLong, $search, $tagsArray, $distanceLimit)





* Visibility: **public**


#### Arguments
* $userLat **mixed**
* $userLong **mixed**
* $search **mixed**
* $tagsArray **mixed**
* $distanceLimit **mixed**



### getReccomendationResults

    mixed Wastetopia\Model\SearchModel::getReccomendationResults($tagsArray, $currentUserID)





* Visibility: **public**


#### Arguments
* $tagsArray **mixed**
* $currentUserID **mixed**



### getListingIDsFromPostCode

    mixed Wastetopia\Model\SearchModel::getListingIDsFromPostCode($postCode)

Searches for exact post code matches



* Visibility: **public**


#### Arguments
* $postCode **mixed**



### getPostCodeFromListing

    mixed Wastetopia\Model\SearchModel::getPostCodeFromListing($listingID)

Gets the post code of the listing (Not necessary if you've already searched as the information is retrieved in those functions)



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### getLatLongFromPostCode

    array Wastetopia\Model\SearchModel::getLatLongFromPostCode($postCode)

Uses Google API to convert a post code to Latitude and longitude



* Visibility: **public**


#### Arguments
* $postCode **mixed**



### getPostCodeFromLatLong

    array Wastetopia\Model\SearchModel::getPostCodeFromLatLong($latitude, $longitude)

Uses Google API to convert a latitude-longitude pair into a post_code



* Visibility: **public**


#### Arguments
* $latitude **mixed**
* $longitude **mixed**



### getDistanceBetweenUserAndListing

    integer Wastetopia\Model\SearchModel::getDistanceBetweenUserAndListing($userPostCode, $listingPostCode)

Uses Google Distance Matrix API to get distance between two post codes



* Visibility: **public**


#### Arguments
* $userPostCode **mixed**
* $listingPostCode **mixed**


