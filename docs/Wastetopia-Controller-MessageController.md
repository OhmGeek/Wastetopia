Wastetopia\Controller\MessageController
===============

Class MessageController - Used for generating and handling input on the Messaging page




* Class name: MessageController
* Namespace: Wastetopia\Controller







Methods
-------


### __construct

    mixed Wastetopia\Controller\MessageController::__construct()

MessageController constructor.



* Visibility: **public**




### generatePageFromListing

    \Wastetopia\Controller\HTML Wastetopia\Controller\MessageController::generatePageFromListing($listingID)

Creates the whole page based ona listingID (using the current logged in user)
Based on principle that user can only have one request for a given listing at any given time



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### generatePage

    string Wastetopia\Controller\MessageController::generatePage($conversationID)

Generates (and prints) HTML for messaging page with initial conversation loaded



* Visibility: **public**


#### Arguments
* $conversationID **mixed**



### generateMessageDisplay

    mixed Wastetopia\Controller\MessageController::generateMessageDisplay($conversationID)

Generates the HTML for the messages in the message box (Use this function to update the messages on the page)



* Visibility: **public**


#### Arguments
* $conversationID **mixed**



### generateItemViewPanel

    mixed Wastetopia\Controller\MessageController::generateItemViewPanel($conversationID)

Generates the output array for listing view side panel (for use in twig file)



* Visibility: **public**


#### Arguments
* $conversationID **mixed**



### sendMessage

    mixed Wastetopia\Controller\MessageController::sendMessage($conversationID, $message)

Sends a message in the conversation



* Visibility: **public**


#### Arguments
* $conversationID **mixed**
* $message **mixed**


