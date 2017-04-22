Wastetopia\Model\ConversationListModel
===============

Class ConversationListModel - Details about general Conversations




* Class name: ConversationListModel
* Namespace: Wastetopia\Model







Methods
-------


### __construct

    mixed Wastetopia\Model\ConversationListModel::__construct()

ConversationListModel constructor.



* Visibility: **public**




### getUserID

    string Wastetopia\Model\ConversationListModel::getUserID()

Returns the ID of the user currently logged in



* Visibility: **private**




### getConversationsReceiving

    mixed Wastetopia\Model\ConversationListModel::getConversationsReceiving()

Gets all conversations (with users) in which you are receiving an item.



* Visibility: **public**




### getConversationsSending

    mixed Wastetopia\Model\ConversationListModel::getConversationsSending()

Gets all conversations in which you are sending an item



* Visibility: **public**




### getConversationsFromListing

    mixed Wastetopia\Model\ConversationListModel::getConversationsFromListing($listingID)

Gets the conversation associated with a given listing (where the current user is the receiver)



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### createConversation

    mixed Wastetopia\Model\ConversationListModel::createConversation($listingID)

Creates a conversation for a given listing



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### deleteConversation

    mixed Wastetopia\Model\ConversationListModel::deleteConversation($conversationID)

Deletes a conversation and its associated messages



* Visibility: **public**


#### Arguments
* $conversationID **mixed**



### getUserImage

    \Wastetopia\Model\URL Wastetopia\Model\ConversationListModel::getUserImage($userID)

Gets the profile picture of the given user (Possibly will be moved to another model)



* Visibility: **public**


#### Arguments
* $userID **mixed**


