Wastetopia\Model\ViewItemModel
===============






* Class name: ViewItemModel
* Namespace: Wastetopia\Model







Methods
-------


### __construct

    mixed Wastetopia\Model\ViewItemModel::__construct()

ViewItemModel constructor.



* Visibility: **public**




### getUserID

    mixed Wastetopia\Model\ViewItemModel::getUserID()

Get the current userid



* Visibility: **private**




### getItemDetails

    array Wastetopia\Model\ViewItemModel::getItemDetails($listingID)

Get item details about a listingID



* Visibility: **public**


#### Arguments
* $listingID **mixed** - &lt;p&gt;(The ListingID to get item details for)&lt;/p&gt;



### getItemStatus

    array Wastetopia\Model\ViewItemModel::getItemStatus($listingID)

Get the status of an item



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### getTagDetails

    array Wastetopia\Model\ViewItemModel::getTagDetails($listingID)

Get the tags for a listing



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### getImages

    array Wastetopia\Model\ViewItemModel::getImages($listingID)

Get image details for a listing



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### getLocation

    array Wastetopia\Model\ViewItemModel::getLocation($listingID)

Get location for a listing



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### getBarcode

    array Wastetopia\Model\ViewItemModel::getBarcode($listingID)

Get barcode for a listing



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### getAll

    array Wastetopia\Model\ViewItemModel::getAll($listingID)

Returns all details, images and tags relating to a given listing



* Visibility: **public**


#### Arguments
* $listingID **mixed**


