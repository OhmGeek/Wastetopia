Wastetopia\Controller\ConversationListController
===============

Class ConversationListController - Used to generate and handle inputs on the ConversationListPage




* Class name: ConversationListController
* Namespace: Wastetopia\Controller







Methods
-------


### __construct

    mixed Wastetopia\Controller\ConversationListController::__construct()

ConversationListController constructor.



* Visibility: **public**




### generatePage

    mixed Wastetopia\Controller\ConversationListController::generatePage()

Generates HTML for list of people/conversations



* Visibility: **public**




### generateReceivingTabHTML

    mixed Wastetopia\Controller\ConversationListController::generateReceivingTabHTML()

Generates HTML for receiving tab



* Visibility: **public**




### generateSendingTabHTML

    mixed Wastetopia\Controller\ConversationListController::generateSendingTabHTML()

Generates HTML for sending tab



* Visibility: **public**




### createConversationArray

    array Wastetopia\Controller\ConversationListController::createConversationArray($conversations)

Constructs an array with the correct variables, given an array returned by a function from UserModel



* Visibility: **public**


#### Arguments
* $conversations **mixed**



### createNewConversation

    mixed Wastetopia\Controller\ConversationListController::createNewConversation($listingID)

Creates a new conversation between users for a given Listing



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### deleteConversation

    mixed Wastetopia\Controller\ConversationListController::deleteConversation($listingID)

Deletes a conversation from the given listingID and it's associated messages



* Visibility: **public**


#### Arguments
* $listingID **mixed**


