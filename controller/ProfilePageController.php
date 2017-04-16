<?php
/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 03/03/2017
 * Time: 11:24
 */
//TODO: Split tabs into separate Twig files that can be separately loaded
namespace Wastetopia\Controller;
use Wastetopia\Config\CurrentConfig;
use Twig_Loader_Filesystem;
use Twig_Environment;

use Wastetopia\Controller\RegistrationController; // For email verification functions
use Wastetopia\Controller\AnalysisController;   // For Advice Tab
use Wastetopia\Controller\RecommendationController; // For Recommendations and Predictions tab

use Wastetopia\Model\ProfilePageModel;
use Wastetopia\Model\CardDetailsModel;

use Wastetopia\Model\AmazonS3; // Needed for uploading image for profile page


/**
 * Class ProfilePageController - Used to generate and handle inputs on the Profile Page
 * @package Wastetopia\Controller
 */
class ProfilePageController
{

    /**
     * ProfilePageController constructor.
     * @param $ownProfile (1 if current logged in user is viewing their own profile)
     * @param $userID of user whose profile you wish to view (only set if $ownProfile is 0)
     */
    public function __construct($ownProfile, $userID = -1)
    {
        // Sets userID to currentUser or specified user
        if ($ownProfile) {
            $this->userID = $this->getUserID();
        } else {
            $this->userID = $userID;
        }

        // Card details model
        $this->cardDetailsModel = new CardDetailsModel();

        // Profile page model
        $this->model = new ProfilePageModel($this->userID); //Need to include

        //Load Twig environment
        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);
    }


    /**
     * Returns the ID of the user who is currently logged in
     * @return int
     */
    private function getUserID()
    {
        // $reader = new UserCookieReader();
        // return $reader->get_user_id();
        return 6; // Usually 6
    }

    /**
     * Returns True if getUserID doesn't return "" or null
     * @return bool True if user is logged in
     */
    function isUserLoggedIn(){
        return $this->getUserID() !== "";
    }

	
    /**
     * Generates HTML for the whole page
     * @return HTML
     */
    function generatePage(){
        $profileContentHTML = $this->generateProfileContentHTML();

        $CurrentConfig = new CurrentConfig();
        $config = $CurrentConfig->getAll();
        $output =  array(
            "config" => $config,
            "profileContent" => $profileContentHTML
        );
        $template = $this->twig->loadTemplate("/users/profile.twig");
        return $template->render($output);
    }


    /**
     * Generates the main HTML part of the page (only the body)
     * @return HTML
     */
    function generateProfileContentHTML()
    {
        $userInformation = $this->generateProfileSection();
        $isCurrentUser = ($this->userID == $this->getUserID() ? 1 : 0); // 1 if logged in user trying to view their own profile

        $homeTabHTML = $this->generateHomeSection();
        $listingsTabHTML = $this->generateListingsSection();
        $offersTabHTML = $this->generateOffersSection();
        $requestsTabHTML = $this->generateRequestsSection();
        $watchListTabHTML = $this->generateWatchListSection();
        $analysisTabHTML = $this->generateAnalysisTabHTML();

        $output = array(
            "isUser" => $isCurrentUser,
            "userID" => $userInformation["userID"],
            "userimage" => $userInformation["userimage"],
            "username" => $userInformation["username"],
            "userscore" => $userInformation["userscore"],
            "homeTab" => $homeTabHTML,
            "listingsTab" => $listingsTabHTML,
            "offersTab" => $offersTabHTML,
            "requestsTab" => $requestsTabHTML,
            "watchlistTab" => $watchListTabHTML,
            "analysisTab" => $analysisTabHTML
        );

        $template = $this->twig->loadTemplate('users/profileContent.twig');
        return $template->render($output);
    }



    /**
     * Generates array of profile information for the website (userimage, username etc)
     * @return array
     */
    function generateProfileSection()
    {
        //Get user details
        $userDetails = $this->cardDetailsModel->getUserDetails($this->userID);
        $userImage = $this->cardDetailsModel->getUserImage($this->userID);
        $userInformation = array();
        $userInformation["userID"] = $userDetails["UserID"];
        $userInformation["username"] = $userDetails["Forename"] . " " . $userDetails["Surname"];
//        $userInformation["email"] = $userDetails["Email_Address"];
        $userInformation["userscore"] = round($userDetails["Mean_Rating_Percent"], 1);
        $userInformation["userimage"] = $userImage;
        return $userInformation;
    }


    /**
     * Generates HTML for Home tab
     * @return HTML
     */
    function generateHomeSection()
    {
        //Get listings user has put up
        $userListingsSending = $this->model->getUserListings();

        // Total number of listings the user has offered
        // Should this be changed to not include completed listings?? (i.e 0 quantity)
        $listingsCount = count($userListingsSending);

        $sendingPendingTransactionsCount = 0;
        $sendingCompletedTransactionsCount = 0;
        $totalAvailableListings = 0; // total number of listings with quantity > 0
        $totalEmptyListings = 0; // total number of listings with 0 quantity

        //Counts number of transactions, available listings, and out-of-stock listings
        foreach ($userListingsSending as $listing) {
            $listingID = $listing["ListingID"];
            $listingQuantity = $listing["Quantity"]; // Will be 0 if listing has run out
            $active = $listing["Active"]; // Will be 0 if listing no longer available

            //Get details about the transactions involving this listing
            $stateDetails = $this->model->getStateOfListingTransaction($listingID);

            //Count number of relevant transactions
            if (count($stateDetails) > 0) {
                foreach ($stateDetails as $transaction) {
                    $completed = $transaction["Success"]; //2-rejected. 1-completed. 0-pending
                    $giverHide = $transaction["Sender_Hide"]; // 1 if Giver of item no longer wants to see transaction
                    if ($completed == 1 && !$giverHide) {
                        $sendingCompletedTransactionsCount += 1;
                    }elseif($completed == 0 && !$giverHide){
                        $sendingPendingTransactionsCount += 1;
                    }else{
                        //Do nothing
                    }
                }
            }
            if ($active) {
                // Check whether it has quantity or not
                if ($listingQuantity > 0) {
                    $totalAvailableListings += 1;
                } else {
                    $totalEmptyListings += 1;
                }
            }
        }

        //Get listings user is receiving
        $userListingsReceiving = $this->model->getUserReceivingListings();

        // Total number of listings the user has requested
        // Should this be changed to not include completed listings?
        $pendingRequestingCount = 0;
        $completedRequestingCount = 0;
        
        //Counts number of transactions for listings user has put up
        foreach ($userListingsReceiving as $listing) {
            $completed = $listing["Success"]; // Transaction completed?
            $receiverHide = $listing["Receiver_Hide"]; // 1 if receiver of item no longer wants to see it
            if ($completed == 1 && !$receiverHide) {
                $completedRequestingCount += 1;
            }elseif($completed == 0 && !$receiverHide){
                $pendingRequestingCount += 1;
            }else{
                //Do nothing
            }
        }

        // Get count for watch list tab
        $watchedListings = $this->model->getWatchedListings($this->getUserID());
        $watchListCount = count($watchedListings);

        // Get Recommendation and Prediction HTML
        $recommendationHTML = $this->generateRecommendationHTML();
        $predictionHTML = $this->generatePredictionHTML();

        $isCurrentUser = ($this->userID == $this->getUserID() ? 1 : 0);
        
        $sendingTransactionsCount = $sendingCompletedTransactionsCount + $sendingPendingTransactionsCount;
        $requestingCount = $pendingRequestingCount + $completedRequestingCount;

        // Get advice text
        $adviceText = $this->generateAdviceText();

        $listingsInformation = array(
            "listingsCount" => $totalAvailableListings, // Total number of listings with quantity > 0
            "emptyListingsCount" => $totalEmptyListings, // Total number of listings with quantity <= 0
            "itemsOfferedCount" => $sendingTransactionsCount, // Total of all transactions for your items (can be greater than listings count)
            "requestsMadeCount" => $requestingCount, // Total of all transactions you're in for other user's items
            "watchListCount" => $watchListCount,
            "recommendationHTML" => $recommendationHTML,
            "predictionHTML" => $predictionHTML,
            "isUser" => $isCurrentUser,
            "adviceText" => $adviceText
        );

        $template = $this->twig->loadTemplate("/users/homeTab.twig");
        return $template->render($listingsInformation);
    }


    /**
     * Generates HTML for Offers tab (Requests for user's items)
     * @return HTML
     */
    function generateOffersSection(){
        $isLoggedIn = $this->isUserLoggedIn(); // 1 if user is a logged in user
	    
        //Get listings user has put up
        $userListingsSending = $this->model->getUserListings();

        $pendingSending = array();    //Incomplete transactions (List of TransactionIDs)
        $completedSending = array();  //Complete transactions (List of TransactionIDs

        //Split listings into complete and pending transactions
        foreach ($userListingsSending as $listing) {
            $listingID = $listing["ListingID"];

            //Get details about the transactions involving this listing
            $stateDetails = $this->model->getStateOfListingTransaction($listingID);

            //If no transactions, this listing will not be in the history page
            if (count($stateDetails) > 0) {
                foreach ($stateDetails as $transaction) {
                    $transactionID = $transaction["TransactionID"];
                    $completed = $transaction["Success"]; //1-completed. 0-pending. 2-rejected
                    $giverHide = $transaction["Sender_Hide"]; // Get from transaction details, flag for if user wants to see this transaction or not
                    if ($completed == 1 && !$giverHide) {
                        array_push($completedSending, $transactionID); //Get display information later
                    }elseif($completed == 0 && !$giverHide) {
                        array_push($pendingSending, $transactionID);   //Get display information later
                    }else{
                        //Do nothing	- it transaction has been rejected
                    }
                }
            }
        }

        $completedOffers = array();
        $pendingOffers = array();

        // Completed sending transactions
        foreach ($completedSending as $transactionID) {
            $transactionDetails = $this->model->getDetailsFromTransactionID($transactionID);
            // Info specific to transaction
            $transactionQuantity = $transactionDetails["Quantity"];
            $completedDate = $transactionDetails["Time_Of_Acceptance"];
            //In for about user involved in this transaction
            $requestingUserID = $transactionDetails["UserID"];
            $requestingUserName = $transactionDetails["Forename"] . " " . $transactionDetails["Surname"];
            $requestingUserImage = $this->cardDetailsModel->getUserImage($requestingUserID);
            // Information about the item/listing involved
            $listingID = $transactionDetails["ListingID"];
            $listingDetails = $this->cardDetailsModel->getCardDetails($listingID);
            $itemName = $listingDetails["Name"];
            $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);
            $item = array(
                "transactionID" => $transactionID,
                "completedDate" => $completedDate,
                "imgURL" => $defaultImage,
                "itemName" => $itemName,
                "userImg" => $requestingUserImage,
                "userID" => $requestingUserID,
                "userName" => $requestingUserName,
                "quantity" => $transactionQuantity,
                "listingID" => $listingID
            );
            array_push($completedOffers, $item);
        }

        // Pending sending transactions
        foreach ($pendingSending as $transactionID) {
            $transactionDetails = $this->model->getDetailsFromTransactionID($transactionID);
            // Info specific to transaction
            $transactionQuantity = $transactionDetails["Quantity"];
            $startedDate = $transactionDetails["Time_Of_Application"];
            //In for about user involved in this transaction
            $requestingUserID = $transactionDetails["UserID"];
            $requestingUserName = $transactionDetails["Forename"] . " " . $transactionDetails["Surname"];
            $requestingUserImage = $this->cardDetailsModel->getUserImage($requestingUserID);
            // Information about the item/listing involved
            $listingID = $transactionDetails["ListingID"];
            $listingDetails = $this->cardDetailsModel->getCardDetails($listingID);
            $itemName = $listingDetails["Name"];
            $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);
            $item = array(
                "transactionID" => $transactionID,
                "startedDate" => $startedDate,
                "imgURL" => $defaultImage,
                "itemName" => $itemName,
                "userImg" => $requestingUserImage,
                "userID" => $requestingUserID,
                "userName" => $requestingUserName,
                "quantity" => $transactionQuantity,
                "listingID" => $listingID,
                "conversationID" => "REPLACE WITH LISTING ID" // Need to figure this out
            );
            array_push($pendingOffers, $item);
        }

        $offers = array("completed" => $completedOffers, "pending" => $pendingOffers);

        $isCurrentUser = ($this->userID == $this->getUserID() ? 1 : 0);

        $listingsInformation = array(
            "offers" => $offers, // Transactions for your items
            "isUser" => $isCurrentUser,
	    "isLoggedIn" => $isLoggedIn
        );

        $template = $this->twig->loadTemplate("/users/offersTab.twig");
        return $template->render($listingsInformation);
    }


    /**
     * Generates HTML for Requests tab
     * @return HTML
     */
    function generateRequestsSection(){
        //Get listings user is receiving
        $userListingsReceiving = $this->model->getUserReceivingListings();
        $pendingReceiving = array();    //Incomplete transactions
        $completedReceiving = array();  //Incomplete transactions

        //Split listings into complete and pending transactions
        foreach ($userListingsReceiving as $listing) {
            $listingID = $listing["ListingID"];
            $transactionID = $listing["TransactionID"];
            $completed = $listing["Success"]; // 1-completed. 0-pending. 2-rejected
            $receiverHide = $listing["Receiver_Hide"];; // Get from listing details, 1 if sender still wants to view the listing transaction
            if ($completed == 1 && !$receiverHide) {
                array_push($completedReceiving, $transactionID); //Get display information later
            } elseif($completed == 0 && !$receiverHide) {
                array_push($pendingReceiving, $transactionID);   //Get display information later
            }else{
                // Do nothing - Rejected it
            }
        }

        $completedRequests = array(); // Final array for completed requests
        $pendingRequests = array(); // Final array for pending requests

        // Completed requesting transactions
        foreach ($completedReceiving as $transactionID) {
            $transactionDetails = $this->model->getDetailsFromTransactionID($transactionID);
            // Info specific to transaction
            $transactionQuantity = $transactionDetails["Quantity"];
            $completedDate = $transactionDetails["Time_Of_Acceptance"];
            // Information about the item/listing involved
            $listingID = $transactionDetails["ListingID"];
            $listingDetails = $this->cardDetailsModel->getCardDetails($listingID);
            $itemName = $listingDetails["Name"];
            $timeOfCreation = $listingDetails["Time_Of_Creation"];
            $postCode = $listingDetails["Post_Code"];
            $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);
            // Owner's details
            $offeringUserID = $listingDetails["UserID"];
            $offeringUserName = $listingDetails["Forename"] . " " . $listingDetails["Surname"];
            $offeringUserImage = $this->cardDetailsModel->getUserImage($offeringUserID);
            $hasRated = $this->model->hasRated($transactionID, $this->getUserID());
            $item = array(
                "transactionID" => $transactionID,
                "completedDate" => $completedDate,
                "userImg" => $offeringUserImage,
                "userID" => $offeringUserID,
                "userName" => $offeringUserName,
                "addedDate" => $timeOfCreation,
                "distance" => "CAN'T GET THIS INFORMATION",
                "imgURL" => $defaultImage,
                "itemName" => $itemName,
                "quantity" => $transactionQuantity,
                "listingID" => $listingID,
                "postCode" => $postCode,
                "hasRated" => $hasRated
            );
            array_push($completedRequests, $item);
        }

        // Pending requesting transactions
        foreach ($pendingReceiving as $transactionID) {
            $transactionDetails = $this->model->getDetailsFromTransactionID($transactionID);
            // Info specific to transaction
            $transactionQuantity = $transactionDetails["Quantity"];
            $startedDate = $transactionDetails["Time_Of_Application"];
            // Information about the item/listing involved
            $listingID = $transactionDetails["ListingID"];
            $listingDetails = $this->cardDetailsModel->getCardDetails($listingID);
            $itemName = $listingDetails["Name"];
            $timeOfCreation = $listingDetails["Time_Of_Creation"];
            $postCode = $listingDetails["Post_Code"];
            $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);
        
            // Owner's details
            $offeringUserID = $listingDetails["UserID"];
            $offeringUserName = $listingDetails["Forename"] . " " . $listingDetails["Surname"];
            $offeringUserImage = $this->cardDetailsModel->getUserImage($offeringUserID);
            $item = array(
                "transactionID" => $transactionID,
                "startedDate" => $startedDate,
                "userImg" => $offeringUserImage,
                "userID" => $offeringUserID,
                "userName" => $offeringUserName,
                "addedDate" => $timeOfCreation,
                "distance" => "CAN'T GET THIS INFORMATION",
                "imgURL" => $defaultImage,
                "itemName" => $itemName,
                "quantity" => $transactionQuantity,
                "listingID" => $listingID,
                "conversationID" => "SAME AS LISTING ID?",
                "postCode" => $postCode
            );
            array_push($pendingRequests, $item);
        }

        $requests = array("completed" => $completedRequests, "pending" => $pendingRequests);

        $isCurrentUser = ($this->userID == $this->getUserID() ? 1 : 0);
        $listingsInformation = array(
            "requests" => $requests, // Transactions for other user's items
            "isUser" => $isCurrentUser
        );

        $template = $this->twig->loadTemplate("/users/requestsTab.twig");
        return $template->render($listingsInformation);
    }


    /**
     * Generates HTML for Listings tab
     * @return HTML
     */
    function generateListingsSection(){
        //Get listings user has put up
        $userListingsSending = $this->model->getUserListings();

        $availableListingsSending = array();
        $emptyListingsSending = array();

        foreach ($userListingsSending as $listing) {
            $listingID = $listing["ListingID"];
            $listingQuantity = $listing["Quantity"]; // Will be 0 if listing has run out
            $active = $listing["Active"]; // Will be 0 if user no longer wants to see it
            // Only process active listings

            if($active){
                // Check whether it has quantity or not
                if ($listingQuantity > 0){
                    array_push($availableListingsSending, $listingID);
                }else{
                    array_push($emptyListingsSending, $listingID);
                }
            }
        }

        // NOW GET APPROPRIATE INFORMATION FROM EACH LISTING
        $allAvailableListings = array();
        $allEmptyListings = array();

        // Get information for all user's listings
        foreach ($availableListingsSending as $listingID) {
            $details = $this->cardDetailsModel->getCardDetails($listingID);
//            $itemID = $details["ItemID"];
            $itemName = $details["Name"];
            $timeOfCreation = $details["Time_Of_Creation"];
            $quantity = $details["Quantity"];
            $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);
            $isRequesting = $this->cardDetailsModel->isRequesting($listingID, $this->getUserID());
            $isWatching = $this->cardDetailsModel->isWatching($listingID, $this->getUserID());
            $item = array(
                "listingID" => $listingID,
                "itemName" => $itemName,
                "addedDate" => $timeOfCreation,
                "quantity" => $quantity,
                "imgURL" => $defaultImage,
                "isRequesting" => $isRequesting,
                "isWatching" => $isWatching);
            array_push($allAvailableListings, $item);
        }
        // Get information for all user's empty listings
        foreach ($emptyListingsSending as $listingID) {
            $details = $this->cardDetailsModel->getCardDetails($listingID);
//            $itemID = $details["ItemID"];
            $itemName = $details["Name"];
            $timeOfCreation = $details["Time_Of_Creation"];
            $quantity = $details["Quantity"];
            $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);

            // Figure out whether to display "Request" or "Cancel request" button
            // $isRequesting = $this->cardDetailsModel->isUserRequestingListing($this->userID, $listingID);

            $item = array(
                "listingID" => $listingID,
                "itemName" => $itemName,
                "addedDate" => $timeOfCreation,
                "quantity" => $quantity,
                "imgURL" => $defaultImage);
            array_push($allEmptyListings, $item);
        }

        $userListings = array("available" => $allAvailableListings, "outOfStock" => $allEmptyListings);

        $isCurrentUser = ($this->userID == $this->getUserID() ? 1 : 0);
        $listingsInformation = array(
            "userListings" => $userListings, // All your listings
            "isUser" => $isCurrentUser
        );

        $template = $this->twig->loadTemplate("/users/listingsTab.twig");
        return $template->render($listingsInformation);
    }


    /**
     * Generates the HTML for the watch list section
     * @return HTML
     */
    function generateWatchListSection()
    {
        //Get IDs of listings user is watching
        $watchedListings = $this->model->getWatchedListings($this->getUserID());
        $count = count($watchedListings);
        $watchList = array();
        foreach ($watchedListings as $listing) {
            $watchID = $listing["WatchID"];
            $listingID = $listing["ListingID"];
            // Get details about the listing
            $details = $this->cardDetailsModel->getCardDetails($listingID);

            $itemName = $details["Name"];
            $quantity = $details["Quantity"];
            $distance = -1; // Can't get this information
            $postCode = $details["Post_Code"];
            $addedDate = $details["Time_Of_Creation"];
            $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);

            // Owner's details
            $userID = $details["UserID"];
            $userImage = $this->cardDetailsModel->getUserImage($userID);
            $userName = $details["Forename"] . " " . $details["Surname"];

            $isRequesting = $this->cardDetailsModel->isRequesting($listingID, $this->getUserID());
            $item = array(
                "listingID" => $listingID,
                "userImg" => $userImage,
                "userID" => $userID,
                "userName" => $userName,
		        "quantity" => $quantity,
                "addedDate" => $addedDate,
                "postCode" => $postCode,
                "imgURL" => $defaultImage,
                "itemName" => $itemName,
                "isRequesting" => $isRequesting
            );

            array_push($watchList, $item);
        }
        $isCurrentUser = ($this->userID == $this->getUserID() ? 1 : 0);
        $template = $this->twig->loadTemplate("/users/watchlistTab.twig");
        return $template->render(array("watchList"=>$watchList, "isUser" => $isCurrentUser));

        //return $watchListDetails;
    }


    /**
     * Generates HTML for recommendation section
     * @return mixed
     */
    function generateRecommendationHTML(){
        $controller = new RecommendationController();
        return $controller->generateRecommendedSection();
    }
    

    /**
     * Generates HTML for prediction section
     * @return mixed
     */
    function generatePredictionHTML(){
        $controller = new RecommendationController();
        return $controller->generatePredictionSection();
    }


    /**
     * Returns the text that goes on the Tile for analysis in the home tab
     * @return string
     */
    function generateAdviceText(){
        $controller = new AnalysisController();

        // Get most frequent Name and Tag in Type category
        $name = $controller->getMostFrequentItemNameSent();
        $tag = $controller->getMostFrequentTypeTagSent();

        if ($name == "" &&  $tag == ""){
            $advice = "Not enough data to give advice, sorry!";
        }elseif($name == "" && $tag!= ""){
            $advice = "You have given away a lot of ".$tag." products. Think about buying less of them, or using them more.";
        }elseif($name !== "" && $tag == ""){
            $advice = "You have given away " . $name . " a lot. If you're not using it, think about buying it less.";
        }else{
            $advice = "You have given away a lot of " .$tag." products, and the item you've given away most is " .$name .". If you have a lot of waste like this, think about buying less of it, or using it more.";
        }

        return $advice;

    }


    /**
     * Returns the HTML for the AnalysisTab on the profile page
     * @return mixed
     */
    function generateAnalysisTabHTML(){
        // Generate the HTML for the Analysis tab but don't load?
        $controller = new AnalysisController();
        return $controller -> generatePage();
    }


    /* Returns true if $listingID is in the current user's watch list*/
    /**
     * @param $listingID
     * @return bool (1 if in user's watch list)
     */
    function inWatchList($listingID){
        return $this->cardDetailsModel->isWatching($listingID, $this->getUserID());
    }


    /**
     * Either adds or deletes a listing from the current user's watch list
     * @param $listingID
     * @return int
     */
    function toggleWatchListListing($listingID){
        $listingID = (int)$listingID;
        if ($this->inWatchList($listingID)){
            $this->model->deleteFromWatchList($listingID, $this->getUserID());
            return 1; // Code for deletion
        }else{
            $this->model->addToWatchList($listingID, $this->getUserID());
            return 2; // Code for added to watch list
        }
    }
    
    
    /**
    * Sets all pending requests to viewed
    * @return bool
    */
    function setAllPendingAsViewed(){
        $listingTransactions = $this->model->getUnseenPendingTransactions();
        foreach($listingTransactions as $listingTransaction){
            $transactionID = $listingTransaction["TransactionID"];
            $listingID = $listingTransaction["ListingID"];
            $this->model->setViewed($listingID, $transactionID);
        }
        return True;
    }
    
    
    /**
    * Sets the Sender_Hide or Receiver_Hide flag to the given value for the given transactionID
    * @pram $giverOrReceiver - 1 for Giver_Hide, 0 for Receiver_Hide
    * @param $transactionID
    * @param $value
    * @return bool
    */
    function setListingTransactionHiddenFlag($giverOrReceiver, $transactionID, $value){
        return $this->model->setListingTransactionHiddenFlag($giverOrReceiver, $transactionID, $value);
    }
    
    
    
    /**
    * Replaces the old password with the new password
    * @param $oldPassword
    * @param $newPassword
    * @return JSON with "error" or "success" message
    */
     function changePassword($oldPassword, $newPassword){
        $userID = $this->getUserID(); 
	    // Get password hash and salt from the database
         $passwordDetails = $this->model->getPasswordDetails($userID);
         $passwordHash = $passwordDetails["Password_Hash"];
         $passwordSalt = $passwordDetails["Salt"];
         
         // hash old password with salt
         $oldPasswordHash = hash('sha256',$passwordSalt.$oldPassword);
        
        // If it matches, change password
         if($oldPasswordHash !== $passwordHash){
            return $this->errorMessage("Current password is incorrect");   
         }else{
            $this->model->updatePassword($newPassword);
	
            // Log out user - NOT SURE HOW
		 
            return $this->successMessage("Password changed"); 
         }
	    
    }
	
    
    /**
    * Resets the password and sends it to user (not secure)
    * @return bool
    */
    function resetPassword(){
	$userID = $this->getUserID();     
        $newPassword = $this->model->generateSalt(8, 10);
	 
	// Update the password    
	$this->model->updatePassword($userID, $newPassword);
	    
	$email = $this->model->getUserEmail($userID);    
	    
	// Send it to user
	$this->sendPasswordEmail($email);   
	    
	return True;    
    }
	
	
    /**
    * Lets user change their email address in the DB
    * @param $oldEmail
    * @param $newEmail
    * @return bool
    */
    function changeEmail($oldEmail, $newEmail){
        $userID = $this->getUserID();
        $actualEmail = $this->model->getUserEmail($userID);

        if($actualEmail !== $oldEmail){
           return $this->errorMessage("Incorrect old email");
        }
        $registrationController = new RegistrationController();
        if(!$registrationController->checkValidEmail($newEmail)){
           return $this->errorMessage("Email is not valid");
        }

        if(!$registrationController->checkAvailable($newEmail)){
            return $this->errorMessage("Email already in use");
        }

        // Reset account
        $this->model->resetAccount($userID);

        // Log user out - NOT SURE ABOUT THIS

        // Send verification email
        $registrationController->sendVerificationEmail($newEmail, $newEmail);

        return $this->successMessage("New verification email sent to your specified email");
    }
	
    /**
    * Replaces the user's profile picture with that at the given URL
    * @param $files - (from Javascripts FormData)
    * @return bool
    */	
    function changeProfilePicture($files){
	    $amazonModel = new AmazonS3();
        $urls = $amazonModel->upload($files);
        $url = $urls[0];
	    return $this->model->changeProfilePicture($url);
    }

    /**
     * Returns JSON enoding of error message - {"error": error}
     * @param $e
     * @return string
     */
    function errorMessage($e){
        $errorArray = array("error" => $e);
        return json_encode($errorArray);
    }

    /**
     * Returns JSON enoding of success message - {"success": success}
     * @param $s
     * @return string
     */
    function successMessage($s){
        $successArray = array("success" => $s);
        return json_encode($successArray);
    }
    
	
    /** 
    * Sends an email to the user with their password in
    * @param $email
    * @param $password
    */
    function sendPasswordEmail($email, $password){
        $CurrentConfig = new CurrentConfig();
        $config = $CurrentConfig->getAll();
            
        $code = $this->model->getVerificationCode($email);
        if($code == -1){
            return False;   
        }
        
        $root = $config["ROOT_BASE"]; // Base url for the website
        $fullURL = $root."/register/verify/".$code; // Verification URL
        
        $to=$email;
        $subject="Reset password";
        $from = 'wastetopia@ohmgeek.co.uk'; 
        $body= 'Your new password is '.$password;
        $altBody = 'Your new password is '.$password;

        $this->sendEmail($from, $subject, $body, $altBody, $email, $email); // Send the email
        
      
    }
	
	
    /**
    * Main function to send an email
    * @param $from
    * @param $subject
    * @param $body
    * @param $altBody
    * @param $email
    * @param $name
    * @return bool
    */
    function sendEmail($from, $subject, $body, $altBody, $email, $name){	    
	$CurrentConfig = new CurrentConfig();
        $config = $CurrentConfig->getAll();
	    
	// PHPMailer code
	$mail = new \PHPMailer(true); //true makes it give errors
        $mail->IsSMTP();                                      // set mailer to use SMTP
        $mail->Host = $config["EMAIL_HOST"]; // For SSL, use mail3.gridhost.co.uk, else try mail.ohmgeek.co.uk
        $mail->Port = $config["EMAIL_PORT"]; //25 for non-SSL, 465  for SSL, 587 for tls
        
        $mail->SMTPSecure = $config["EMAIL_SECURITY"]; 
  
        $mail->SMTPAuth = true;     // turn on SMTP authentiocation
        
        $mail->Username = $config["EMAIL_ADDRESS"];  // SMTP username
        $mail->Password = $config["EMAIL_PASSWORD"]; // SMTP password (IHatePHP  or wyI4wwPRhHGk)
        $mail->From = $from;
        $mail->FromName = "Wastetopia";
        
        $mail->AddAddress($email, $name);
       
        $mail->WordWrap = 50;                                 // set word wrap to 50 characters
        $mail->IsHTML(true);                                  // set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody;
        if(!$mail->Send())
        {
           return False;
        }
        return True;    
    }
}
