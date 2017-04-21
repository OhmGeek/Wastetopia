Wastetopia\Controller\EditItemController
===============






* Class name: EditItemController
* Namespace: Wastetopia\Controller







Methods
-------


### __construct

    mixed Wastetopia\Controller\EditItemController::__construct($listingID)





* Visibility: **public**


#### Arguments
* $listingID **mixed**



### isOwner

    boolean Wastetopia\Controller\EditItemController::isOwner()

Checks whether the current user owns the listing



* Visibility: **private**




### renderEditPage

    string Wastetopia\Controller\EditItemController::renderEditPage()





* Visibility: **public**




### getListOfTagsForView

    array Wastetopia\Controller\EditItemController::getListOfTagsForView()





* Visibility: **private**




### generateTags

    array Wastetopia\Controller\EditItemController::generateTags($details)





* Visibility: **private**


#### Arguments
* $details **mixed** - &lt;p&gt;(the item object as serialized by JavaScript)&lt;/p&gt;



### getImageArray

    array Wastetopia\Controller\EditItemController::getImageArray($details)





* Visibility: **private**


#### Arguments
* $details **mixed** - &lt;p&gt;(the item object as serialized by JavaScript)&lt;/p&gt;



### addItem

    mixed Wastetopia\Controller\EditItemController::addItem($details)

Add an item to the DB



* Visibility: **public**


#### Arguments
* $details **mixed** - &lt;p&gt;(a serialized item)&lt;/p&gt;



### addItemImage

    string Wastetopia\Controller\EditItemController::addItemImage($files)

Add an item image to S3 and to the DB



* Visibility: **public**


#### Arguments
* $files **mixed** - &lt;p&gt;(the files to upload)&lt;/p&gt;


