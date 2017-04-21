Wastetopia\Controller\ProfilePageController
===============

Class ProfilePageController - Used to generate and handle inputs on the Profile Page




* Class name: ProfilePageController
* Namespace: Wastetopia\Controller







Methods
-------


### __construct

    mixed Wastetopia\Controller\ProfilePageController::__construct($ownProfile, $userID)

ProfilePageController constructor.



* Visibility: **public**


#### Arguments
* $ownProfile **mixed** - &lt;p&gt;(1 if current logged in user is viewing their own profile)&lt;/p&gt;
* $userID **mixed** - &lt;p&gt;of user whose profile you wish to view (only set if $ownProfile is 0)&lt;/p&gt;



### getUserID

    integer Wastetopia\Controller\ProfilePageController::getUserID()

Returns the ID of the user who is currently logged in



* Visibility: **private**




### isUserLoggedIn

    boolean Wastetopia\Controller\ProfilePageController::isUserLoggedIn()

Returns True if getUserID doesn't return "" or null



* Visibility: **public**




### generatePage

    string Wastetopia\Controller\ProfilePageController::generatePage()

Generates HTML for the whole page



* Visibility: **public**




### generateProfileContentHTML

    string Wastetopia\Controller\ProfilePageController::generateProfileContentHTML()

Generates the main HTML part of the page (only the body)



* Visibility: **public**




### generateProfileSection

    array Wastetopia\Controller\ProfilePageController::generateProfileSection()

Generates array of profile information for the website (userimage, username etc)



* Visibility: **public**




### generateHomeSection

    string Wastetopia\Controller\ProfilePageController::generateHomeSection()

Generates HTML for Home tab



* Visibility: **public**




### generateOffersSection

    \Wastetopia\Controller\HTML Wastetopia\Controller\ProfilePageController::generateOffersSection()

Generates HTML for Offers tab (Requests for user's items)



* Visibility: **public**




### generateRequestsSection

    \Wastetopia\Controller\HTML Wastetopia\Controller\ProfilePageController::generateRequestsSection()

Generates HTML for Requests tab



* Visibility: **public**




### generateListingsSection

    \Wastetopia\Controller\HTML Wastetopia\Controller\ProfilePageController::generateListingsSection()

Generates HTML for Listings tab



* Visibility: **public**




### generateWatchListSection

    \Wastetopia\Controller\HTML Wastetopia\Controller\ProfilePageController::generateWatchListSection()

Generates the HTML for the watch list section



* Visibility: **public**




### generateRecommendationHTML

    mixed Wastetopia\Controller\ProfilePageController::generateRecommendationHTML()

Generates HTML for recommendation section



* Visibility: **public**




### generatePredictionHTML

    mixed Wastetopia\Controller\ProfilePageController::generatePredictionHTML()

Generates HTML for prediction section



* Visibility: **public**




### generatePredictionNames

    array Wastetopia\Controller\ProfilePageController::generatePredictionNames()

Gets the top 3 names of items the user gives away



* Visibility: **public**




### generateAdviceText

    string Wastetopia\Controller\ProfilePageController::generateAdviceText()

Returns the text that goes on the Tile for analysis in the home tab



* Visibility: **public**




### generateAnalysisTabHTML

    mixed Wastetopia\Controller\ProfilePageController::generateAnalysisTabHTML()

Returns the HTML for the AnalysisTab on the profile page



* Visibility: **public**




### inWatchList

    boolean Wastetopia\Controller\ProfilePageController::inWatchList($listingID)





* Visibility: **public**


#### Arguments
* $listingID **mixed**



### toggleWatchListListing

    integer Wastetopia\Controller\ProfilePageController::toggleWatchListListing($listingID)

Either adds or deletes a listing from the current user's watch list



* Visibility: **public**


#### Arguments
* $listingID **mixed**



### setAllPendingAsViewed

    boolean Wastetopia\Controller\ProfilePageController::setAllPendingAsViewed()

Sets all pending requests to viewed



* Visibility: **public**




### setListingTransactionHiddenFlag

    boolean Wastetopia\Controller\ProfilePageController::setListingTransactionHiddenFlag($giverOrReceiver, $transactionID, $value)

Sets the Sender_Hide or Receiver_Hide flag to the given value for the given transactionID



* Visibility: **public**


#### Arguments
* $giverOrReceiver **mixed**
* $transactionID **mixed**
* $value **mixed**



### changePassword

    \Wastetopia\Controller\JSON Wastetopia\Controller\ProfilePageController::changePassword($oldPassword, $newPassword)

Replaces the old password with the new password



* Visibility: **public**


#### Arguments
* $oldPassword **mixed**
* $newPassword **mixed**



### resetPassword

    boolean Wastetopia\Controller\ProfilePageController::resetPassword()

Resets the password and sends it to user (not secure)



* Visibility: **public**




### changeEmail

    boolean Wastetopia\Controller\ProfilePageController::changeEmail($oldEmail, $newEmail)

Lets user change their email address in the DB



* Visibility: **public**


#### Arguments
* $oldEmail **mixed**
* $newEmail **mixed**



### changeProfilePicture

    boolean Wastetopia\Controller\ProfilePageController::changeProfilePicture($files)

Replaces the user's profile picture with that at the given URL



* Visibility: **public**


#### Arguments
* $files **mixed** - &lt;ul&gt;
&lt;li&gt;(from Javascripts FormData)&lt;/li&gt;
&lt;/ul&gt;



### errorMessage

    string Wastetopia\Controller\ProfilePageController::errorMessage($e)

Returns JSON enoding of error message - {"error": error}



* Visibility: **public**


#### Arguments
* $e **mixed**



### successMessage

    string Wastetopia\Controller\ProfilePageController::successMessage($s)

Returns JSON enoding of success message - {"success": success}



* Visibility: **public**


#### Arguments
* $s **mixed**



### sendPasswordEmail

    mixed Wastetopia\Controller\ProfilePageController::sendPasswordEmail($email, $password)

Sends an email to the user with their password in



* Visibility: **public**


#### Arguments
* $email **mixed**
* $password **mixed**



### sendEmail

    boolean Wastetopia\Controller\ProfilePageController::sendEmail($from, $subject, $body, $altBody, $email, $name)

Main function to send an email



* Visibility: **public**


#### Arguments
* $from **mixed**
* $subject **mixed**
* $body **mixed**
* $altBody **mixed**
* $email **mixed**
* $name **mixed**


