Wastetopia\Model\AddItemModel
===============






* Class name: AddItemModel
* Namespace: Wastetopia\Model







Methods
-------


### __construct

    mixed Wastetopia\Model\AddItemModel::__construct()





* Visibility: **public**




### getUserID

    string Wastetopia\Model\AddItemModel::getUserID()

Returns the ID of the user currently logged in



* Visibility: **private**




### getLastItemID

    integer Wastetopia\Model\AddItemModel::getLastItemID($name, $useBy, $description)

Returns the ID of the last item inserted with the given parameters



* Visibility: **private**


#### Arguments
* $name **mixed**
* $useBy **mixed**
* $description **mixed**



### getLastImageID

    integer Wastetopia\Model\AddItemModel::getLastImageID($fileType, $imageURL)

Returns the ID of the last image inserted with the given parameters



* Visibility: **public**


#### Arguments
* $fileType **mixed**
* $imageURL **mixed**



### getLastLocationID

    integer Wastetopia\Model\AddItemModel::getLastLocationID($name, $postCode, $long, $lat)

Returns the ID of the last location inserted with the given parameters



* Visibility: **public**


#### Arguments
* $name **mixed**
* $postCode **mixed**
* $long **mixed**
* $lat **mixed**



### getAllTagOptions

    mixed Wastetopia\Model\AddItemModel::getAllTagOptions($categoryID)

Gets all the select options user can choose for tags



* Visibility: **public**


#### Arguments
* $categoryID **mixed** - &lt;p&gt;(category to search for)&lt;/p&gt;



### addToItemTable

    integer Wastetopia\Model\AddItemModel::addToItemTable($name, $description, $useByDate)

Inserts Item data into the Item table



* Visibility: **public**


#### Arguments
* $name **mixed** - &lt;p&gt;(string name)&lt;/p&gt;
* $description **mixed** - &lt;p&gt;(string description)&lt;/p&gt;
* $useByDate **mixed** - &lt;p&gt;(in the form YYYY-MM-DD)&lt;/p&gt;



### addToItemTagTable

    mixed Wastetopia\Model\AddItemModel::addToItemTagTable($itemID, $tagID)

Links a tag to an item



* Visibility: **public**


#### Arguments
* $itemID **mixed**
* $tagID **mixed**



### addToImageTable

    integer Wastetopia\Model\AddItemModel::addToImageTable($fileType, $imageURL)

Adds to image table



* Visibility: **public**


#### Arguments
* $fileType **mixed** - &lt;p&gt;(string file-type, e.g JPG)&lt;/p&gt;
* $imageURL **mixed** - &lt;p&gt;(string URL to location of image in website)&lt;/p&gt;



### addToItemImageTable

    mixed Wastetopia\Model\AddItemModel::addToItemImageTable($imageID, $itemID, $isDefault)

Links an image to an item



* Visibility: **public**


#### Arguments
* $imageID **mixed**
* $itemID **mixed**
* $isDefault **mixed** - &lt;p&gt;(1 if this image is the main image for the item)&lt;/p&gt;



### addToBarcodeTable

    mixed Wastetopia\Model\AddItemModel::addToBarcodeTable($itemID, $barcode, $barcodeType)

Adds a barcode and links it to the item



* Visibility: **public**


#### Arguments
* $itemID **mixed**
* $barcode **mixed** - &lt;p&gt;(Integer barcode)&lt;/p&gt;
* $barcodeType **mixed** - &lt;p&gt;(String representation of its type)&lt;/p&gt;



### addToLocationTable

    integer Wastetopia\Model\AddItemModel::addToLocationTable($name, $postCode, $long, $lat)

Adds the location of the listing to the location table



* Visibility: **public**


#### Arguments
* $name **mixed** - &lt;p&gt;(String name of location, e.g My house)&lt;/p&gt;
* $postCode **mixed** - &lt;p&gt;(String format)&lt;/p&gt;
* $long **mixed**
* $lat **mixed**



### addToListingTable

    mixed Wastetopia\Model\AddItemModel::addToListingTable($locationID, $itemID, $quantity)

Adds the details to the listing table



* Visibility: **public**


#### Arguments
* $locationID **mixed**
* $itemID **mixed**
* $quantity **mixed**



### addAllTags

    mixed Wastetopia\Model\AddItemModel::addAllTags($itemID, $tags)

Calls functions to add all the tags and then link the tags to the item



* Visibility: **public**


#### Arguments
* $itemID **mixed**
* $tags **mixed** - &lt;p&gt;(Array of tag arrays in the form [&quot;tagID&quot;=&gt;tagID])&lt;/p&gt;



### addAllImages

    mixed Wastetopia\Model\AddItemModel::addAllImages($itemID, $images)

Adds all the images to the database and links them to the item



* Visibility: **public**


#### Arguments
* $itemID **mixed**
* $images **mixed** - &lt;p&gt;(Array of image arrays in the form [&quot;fileType&quot;=&gt;fileType, &quot;url&quot;=&gt;url, &quot;isDefault&quot;=&gt;isDefault])&lt;/p&gt;



### mainAddItemFunction

    mixed Wastetopia\Model\AddItemModel::mainAddItemFunction($item, $tags, $images, $barcode, $location)

Calls all the other linking functions and is the only one needed by the user



* Visibility: **public**


#### Arguments
* $item **mixed** - &lt;p&gt;(Associative array in the form [&quot;itemName&quot;=&gt;name, &quot;itemDescription&quot;=&gt;description, &quot;useByDate&quot;=&gt;date, &quot;quantity&quot;=&gt;quantity])&lt;/p&gt;
* $tags **mixed** - &lt;p&gt;(Array of tag arrays in the form [&quot;tagID&quot;=&gt;tagID]) (Seems like that&#039;s what it&#039;s using)&lt;/p&gt;
* $images **mixed** - &lt;p&gt;(Array of image arrays in the form [&quot;fileType&quot;=&gt;fileType, &quot;url&quot;=&gt;url, &quot;isDefault&quot;=&gt;isDefault])&lt;/p&gt;
* $barcode **mixed** - &lt;p&gt;(Associative array in the form [&quot;barcodeNumber&quot;=&gt;number, &quot;barcodeType&quot;=&gt;type])&lt;/p&gt;
* $location **mixed** - &lt;p&gt;(Associative array in the form [&quot;locationName&quot;=&gt;name, &quot;postCode&quot;=&gt;postCode])&lt;/p&gt;



### getImageIDFromURL

    string Wastetopia\Model\AddItemModel::getImageIDFromURL($imageURL)

Retrieves an ImageID from an uploaded image



* Visibility: **private**


#### Arguments
* $imageURL **mixed** - &lt;p&gt;(URL to search for)&lt;/p&gt;



### getTagDetails

    array Wastetopia\Model\AddItemModel::getTagDetails($name)

Get the details of a tag



* Visibility: **public**


#### Arguments
* $name **mixed** - &lt;p&gt;(The Tag Name)&lt;/p&gt;



### getFinalListingID

    mixed Wastetopia\Model\AddItemModel::getFinalListingID($locationID, $itemID, $quantity)

This fetches the Listing ID from details



* Visibility: **private**


#### Arguments
* $locationID **mixed** - &lt;p&gt;(locationID of the item to fetch)&lt;/p&gt;
* $itemID **mixed** - &lt;p&gt;(itemID of the item to fetch)&lt;/p&gt;
* $quantity **mixed** - &lt;p&gt;(quantity of the item to fetch)&lt;/p&gt;


