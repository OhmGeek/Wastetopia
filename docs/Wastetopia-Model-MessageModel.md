Wastetopia\Model\MessageModel
===============

Class MessageModel - Details of messages within a conversation




* Class name: MessageModel
* Namespace: Wastetopia\Model







Methods
-------


### __construct

    mixed Wastetopia\Model\MessageModel::__construct()

MessageModel constructor.



* Visibility: **public**




### getUserID

    string Wastetopia\Model\MessageModel::getUserID()

Returns the ID of the user currently logged in



* Visibility: **public**




### getConversationIDFromListing

    integer Wastetopia\Model\MessageModel::getConversationIDFromListing($listingID)

Returns the ID of the conversation associated with the given listing and the current logged in user



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### getMessagesFromConversation

    mixed Wastetopia\Model\MessageModel::getMessagesFromConversation($conversationID)

Return all messages in the conversation betweeen you and another user



* Visibility: **public**


#### Arguments
* $conversationID **mixed**



### setMessagesAsRead

    boolean Wastetopia\Model\MessageModel::setMessagesAsRead($conversationID)

Sets all messages in a conversation sent by otherUser to read`



* Visibility: **public**


#### Arguments
* $conversationID **mixed**



### sendMessage

    mixed Wastetopia\Model\MessageModel::sendMessage($conversationID, $message, $giverOrReceiver)

Sends message to specified user



* Visibility: **public**


#### Arguments
* $conversationID **mixed**
* $message **mixed**
* $giverOrReceiver **mixed**



### checkIfReceiver

    boolean Wastetopia\Model\MessageModel::checkIfReceiver($conversationID)





* Visibility: **public**


#### Arguments
* $conversationID **mixed**



### getConversationDetails

    mixed Wastetopia\Model\MessageModel::getConversationDetails($conversationID)

Gets the name of the other person in the conversation and the name of the item being given away



* Visibility: **public**


#### Arguments
* $conversationID **mixed**



### getListingDetails

    mixed Wastetopia\Model\MessageModel::getListingDetails($conversationID)

Gets general details needed for side-panel on messages page



* Visibility: **public**


#### Arguments
* $conversationID **mixed**


