Wastetopia\Controller\AddItemController
===============






* Class name: AddItemController
* Namespace: Wastetopia\Controller







Methods
-------


### __construct

    mixed Wastetopia\Controller\AddItemController::__construct()





* Visibility: **public**




### renderAddPage

    string Wastetopia\Controller\AddItemController::renderAddPage()





* Visibility: **public**




### getListOfTagsForView

    array Wastetopia\Controller\AddItemController::getListOfTagsForView()





* Visibility: **private**




### generateTags

    array Wastetopia\Controller\AddItemController::generateTags($details)





* Visibility: **private**


#### Arguments
* $details **mixed** - &lt;p&gt;(the item object as serialized by JavaScript)&lt;/p&gt;



### getImageArray

    array Wastetopia\Controller\AddItemController::getImageArray($details)





* Visibility: **private**


#### Arguments
* $details **mixed** - &lt;p&gt;(the item object as serialized by JavaScript)&lt;/p&gt;



### addItem

    mixed Wastetopia\Controller\AddItemController::addItem($details)

Add an item to the DB



* Visibility: **public**


#### Arguments
* $details **mixed** - &lt;p&gt;(a serialized item)&lt;/p&gt;



### addItemImage

    string Wastetopia\Controller\AddItemController::addItemImage($files)

Add an item image to S3 and the DB



* Visibility: **public**


#### Arguments
* $files **mixed** - &lt;p&gt;(array of files to upload)&lt;/p&gt;


