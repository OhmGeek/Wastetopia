Wastetopia\Controller\SearchController
===============






* Class name: SearchController
* Namespace: Wastetopia\Controller







Methods
-------


### __construct

    mixed Wastetopia\Controller\SearchController::__construct()





* Visibility: **public**




### recommendationSearch

    mixed Wastetopia\Controller\SearchController::recommendationSearch($tagsArr, $currentUserID)





* Visibility: **public**


#### Arguments
* $tagsArr **mixed**
* $currentUserID **mixed**



### JSONSearch

    mixed Wastetopia\Controller\SearchController::JSONSearch($lat, $long, $search, $tagsArr, $notTagsArr, $distanceLimit, $pageNumber)





* Visibility: **public**


#### Arguments
* $lat **mixed**
* $long **mixed**
* $search **mixed**
* $tagsArr **mixed**
* $notTagsArr **mixed**
* $distanceLimit **mixed**
* $pageNumber **mixed**



### MAPSearch

    mixed Wastetopia\Controller\SearchController::MAPSearch($lat, $long, $search, $tagsArr, $notTagsArr, $distanceLimit)





* Visibility: **public**


#### Arguments
* $lat **mixed**
* $long **mixed**
* $search **mixed**
* $tagsArr **mixed**
* $notTagsArr **mixed**
* $distanceLimit **mixed**



### search

    mixed Wastetopia\Controller\SearchController::search($lat, $long, $search, $tagsArr)





* Visibility: **private**


#### Arguments
* $lat **mixed**
* $long **mixed**
* $search **mixed**
* $tagsArr **mixed**



### haversineDistance

    mixed Wastetopia\Controller\SearchController::haversineDistance($latLong1, $latLong2)





* Visibility: **public**


#### Arguments
* $latLong1 **mixed**
* $latLong2 **mixed**



### haversine

    mixed Wastetopia\Controller\SearchController::haversine($theta)





* Visibility: **public**


#### Arguments
* $theta **mixed**


