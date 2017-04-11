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
use Wastetopia\Model\ProfilePageModel;
use Wastetopia\Model\CardDetailsModel;
use Wastetopia\Controller\RecommendationController;
use Twig_Loader_Filesystem;
use Twig_Environment;
class ProfilePageController
{
    /**
     * ProfilePageController constructor.
     * @param $ownProfile (1 if current logged in user is viewing their own profile)
     * @param $userID of user whose profile you wish to view (only set if $ownProfile is 0)
     */
    public function __construct($ownProfile, $userID = -1)
    {
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


    function generateProfileContentHTML()
    {
        $userInformation = $this->generateProfileSection();
        $isCurrentUser = ($this->userID == $this->getUserID() ? 1 : 0); // 1 if logged in user trying to view their own profile

        $homeTabHTML = $this->generateHomeSection();
        $listingsTabHTML = $this->generateListingsSection();
        $offersTabHTML = $this->generateOffersSection();
        $requestsTabHTML = $this->generateRequestsSection();
        $watchListTabHTML = $this->generateWatchListSection();

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
            "watchlistTab" => $watchListTabHTML
        );

        $template = $this->twig->loadTemplate('users/profileContent.twig');
        return $template->render($output);
    }
    /* Generates the profile information for the website */
    function generateProfileSection()
    {
        //Get user details
        $userDetails = $this->cardDetailsModel->getUserDetails($this->userID);
        $userImage = $this->cardDetailsModel->getUserImage($this->userID);
        $userInformation = array();
        $userInformation["userID"] = $userDetails["UserID"];
        $userInformation["username"] = $userDetails["Forename"] . " " . $userDetails["Surname"];
//        $userInformation["email"] = $userDetails["Email_Address"];
        $userInformation["userscore"] = $userDetails["Mean_Rating_Percent"];
        $userInformation["userimage"] = $userImage;
        return $userInformation;
    }

//    /* Generates the information for the Listings section of the page
//    TODO: Sort out difference between a transaction and a listing
//    TODO: Show users transactions for pending and completed with the quantities from ListingTransaction table */
//    function generateTabsSection()
//    {
//        //Get listings user has put up
//        $userListingsSending = $this->model->getUserListings();
//
//        $pendingSending = array();    //Incomplete transactions (List of TransactionIDs)
//        $completedSending = array();  //Complete transactions (List of TransactionIDs
//        $availableListingsSending = array(); // List of all listingIDs user is giving away that have quantity
//        $emptyListingsSending = array(); // List of all listingIDs user is giving away that have 0 quantity
//        // Total number of listings the user has offered
//        // Should this be changed to not include completed listings?? (i.e 0 quantity)
//        $listingsCount = count($userListingsSending);
//        // total number of transactions that have been made for user's listings
//        $sendingTransactionsCount = 0;
//        //Split listings into complete and pending transactions (and listings with no transactions)
//        foreach ($userListingsSending as $listing) {
//            $listingID = $listing["ListingID"];
//            $listingQuantity = $listing["Quantity"]; // Will be 0 if listing has run out
//            $active = $listing["Active"]; // Will be 0 if user no longer wants to see it
//            // Only process active listings
//
//            //Get details about the transactions involving this listing
//            $stateDetails = $this->model->getStateOfListingTransaction($listingID);
//            //If no transactions, this listing will not be in the history page
//            if (count($stateDetails) > 0) {
//                foreach ($stateDetails as $transaction) {
//
//                    $transactionID = $transaction["TransactionID"];
//                    $completed = $transaction["Success"];
//                    if ($completed == 1) {
//                        $sendingTransactionsCount += 1;
//                        // Need to figure out how to deal with these as transactions
//                        array_push($completedSending, $transactionID); //Get display information later
//                    }elseif($completed == 0) {
//                        $sendingTransactionsCount += 1;
//                        // Need to figure out how to deal with these as transactions
//                        array_push($pendingSending, $transactionID);   //Get display information later
//                    }else{
//                        //Do nothing	- it transaction has been rejected
//                    }
//                }
//            }
//            if($active){
//                // Check whether it has quantity or not
//                if ($listingQuantity > 0){
//                    array_push($availableListingsSending, $listingID);
//                }else{
//                    array_push($emptyListingsSending, $listingID);
//                }
//            }
//        }
//        $totalAvailabaleListings = count($availableListingsSending);
//        $totalEmptyListings = count($emptyListingsSending);
//        //Get listings user is receiving
//        $userListingsReceiving = $this->model->getUserReceivingListings();
//        $pendingReceiving = array();    //Incomplete transactions
//        $completedReceiving = array();  //Incomplete transactions
//
//        // Total number of listings the user has requested
//        // Should this be changed to not include completed listings?
//        $receivingCount = 0;
//
//        //Split listings into complete and pending transactions
//        foreach ($userListingsReceiving as $listing) {
//            $listingID = $listing["ListingID"];
//            $transactionID = $listing["TransactionID"];
//            $completed = $listing["Success"]; // Transaction completed?
//            if ($completed == 1) {
//                $receivingCount += 1;
//                // Need to figure out how to deal with these as transactions
//                array_push($completedReceiving, $transactionID); //Get display information later
//            } elseif($completed == 0) {
//                $receivingCount += 1;
//                // Need to figure out how to deal with these as transactions
//                array_push($pendingReceiving, $transactionID);   //Get display information later
//            }else{
//                // Do nothing - Rejected it
//            }
//        }
//        // NOW GET APPROPRIATE INFORMATION FROM EACH LISTING
//        $allAvailableListings = array();
//        $allEmptyListings = array();
//        $completedOffers = array();
//        $pendingOffers = array();
//        $completedRequests = array();
//        $pendingRequests = array();
//        // Get information for all user's listings
//        foreach ($availableListingsSending as $listingID) {
//            $details = $this->cardDetailsModel->getCardDetails($listingID);
////            $itemID = $details["ItemID"];
//            $itemName = $details["Name"];
//            $timeOfCreation = $details["Time_Of_Creation"];
//            $quantity = $details["Quantity"];
//            $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);
//            $imageURL = $defaultImage["Image_URL"];
//            $isRequesting = $this->model->isRequesting($listingID, $this->getUserID());
//            $isWatching = $this->inWatchList($listingID);
//            $item = array(
//                "listingID" => $listingID,
//                "itemName" => $itemName,
//                "addedDate" => $timeOfCreation,
//                "quantity" => $quantity,
//                "imgURL" => $imageURL,
//                "isRequesting" => $isRequesting,
//                "isWatching" => $isWatching);
//            array_push($allAvailableListings, $item);
//        }
//        // Get information for all user's empty listings
//        foreach ($emptyListingsSending as $listingID) {
//            $details = $this->cardDetailsModel->getCardDetails($listingID);
////            $itemID = $details["ItemID"];
//            $itemName = $details["Name"];
//            $timeOfCreation = $details["Time_Of_Creation"];
//            $quantity = $details["Quantity"];
//            $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);
//            $imageURL = $defaultImage["Image_URL"];
//
//            // Figure out whether to display "Request" or "Cancel request" button
//            // $isRequesting = $this->cardDetailsModel->isUserRequestingListing($this->userID, $listingID);
//
//            $item = array(
//                "listingID" => $listingID,
//                "itemName" => $itemName,
//                "addedDate" => $timeOfCreation,
//                "quantity" => $quantity,
//                "imgURL" => $imageURL);
//            array_push($allEmptyListings, $item);
//        }
//        //NEXT TWO FOR LOOPS ARE ALMOST IDENTICAL, CHANGE NAMES IN TWIG SO THESE CAN BE MADE INTO ONE FUNCTION
//        // Completed sending transactions
//        foreach ($completedSending as $transactionID) {
//            $transactionDetails = $this->model->getDetailsFromTransactionID($transactionID);
//            // Info specific to transaction
//            $transactionQuantity = $transactionDetails["Quantity"];
//            $completedDate = $transactionDetails["Time_Of_Acceptance"];
//            //In for about user involved in this transaction
//            $requestingUserID = $transactionDetails["UserID"];
//            $requestingUserName = $transactionDetails["Forename"] . " " . $transactionDetails["Surname"];
//            $requestingUserImage = $this->cardDetailsModel->getUserImage($requestingUserID);
//            // Information about the item/listing involved
//            $listingID = $transactionDetails["ListingID"];
//            $listingDetails = $this->cardDetailsModel->getCardDetails($listingID);
//            $itemName = $listingDetails["Name"];
//            $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);
//            $imageURL = $defaultImage["Image_URL"];
//            $item = array(
//                "transactionID" => $transactionID,
//                "completedDate" => $completedDate,
//                "imgURL" => $imageURL,
//                "itemName" => $itemName,
//                "userImg" => $requestingUserImage,
//                "userID" => $requestingUserID,
//                "userName" => $requestingUserName,
//                "quantity" => $transactionQuantity,
//                "listingID" => $listingID
//            );
//            array_push($completedOffers, $item);
//        }
//        // Pending sending transactions
//        foreach ($pendingSending as $transactionID) {
//            $transactionDetails = $this->model->getDetailsFromTransactionID($transactionID);
//            // Info specific to transaction
//            $transactionQuantity = $transactionDetails["Quantity"];
//            $startedDate = $transactionDetails["Time_Of_Application"];
//            //In for about user involved in this transaction
//            $requestingUserID = $transactionDetails["UserID"];
//            $requestingUserName = $transactionDetails["Forename"] . " " . $transactionDetails["Surname"];
//            $requestingUserImage = $this->cardDetailsModel->getUserImage($requestingUserID);
//            // Information about the item/listing involved
//            $listingID = $transactionDetails["ListingID"];
//            $listingDetails = $this->cardDetailsModel->getCardDetails($listingID);
//            $itemName = $listingDetails["Name"];
//            $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);
//            $imageURL = $defaultImage["Image_URL"];
//            $item = array(
//                "transactionID" => $transactionID,
//                "startedDate" => $startedDate,
//                "imgURL" => $imageURL,
//                "itemName" => $itemName,
//                "userImg" => $requestingUserImage,
//                "userID" => $requestingUserID,
//                "userName" => $requestingUserName,
//                "quantity" => $transactionQuantity,
//                "listingID" => $listingID,
//                "conversationID" => "REPLACE WITH LISTING ID" // Need to figure this out
//            );
//            array_push($pendingOffers, $item);
//        }
//
//        // Completed requesting transactions
//        foreach ($completedReceiving as $transactionID) {
//            $transactionDetails = $this->model->getDetailsFromTransactionID($transactionID);
//            // Info specific to transaction
//            $transactionQuantity = $transactionDetails["Quantity"];
//            $completedDate = $transactionDetails["Time_Of_Acceptance"];
//            // Information about the item/listing involved
//            $listingID = $transactionDetails["ListingID"];
//            $listingDetails = $this->cardDetailsModel->getCardDetails($listingID);
//            $itemName = $listingDetails["Name"];
//            $timeOfCreation = $listingDetails["Time_Of_Creation"];
//            $postCode = $listingDetails["Post_Code"];
//            $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);
//            $imageURL = $defaultImage["Image_URL"];
//            // Owner's details
//            $offeringUserID = $listingDetails["UserID"];
//            $offeringUserName = $listingDetails["Forename"] . " " . $listingDetails["Surname"];
//            $offeringUserImage = $this->cardDetailsModel->getUserImage($offeringUserID);
//            $hasRated = $this->model->hasRated($transactionID, $this->getUserID());
//            $item = array(
//                "transactionID" => $transactionID,
//                "completedDate" => $completedDate,
//                "userImg" => $offeringUserImage,
//                "userID" => $offeringUserID,
//                "userName" => $offeringUserName,
//                "addedDate" => $timeOfCreation,
//                "distance" => "CAN'T GET THIS INFORMATION",
//                "imgURL" => $imageURL,
//                "itemName" => $itemName,
//                "quantity" => $transactionQuantity,
//                "listingID" => $listingID,
//                "postCode" => $postCode,
//                "hasRated" => $hasRated
//            );
//            array_push($completedRequests, $item);
//        }
//        // Pending requesting transactions
//        foreach ($pendingReceiving as $transactionID) {
//            $transactionDetails = $this->model->getDetailsFromTransactionID($transactionID);
//            // Info specific to transaction
//            $transactionQuantity = $transactionDetails["Quantity"];
//            $startedDate = $transactionDetails["Time_Of_Application"];
//            // Information about the item/listing involved
//            $listingID = $transactionDetails["ListingID"];
//            $listingDetails = $this->cardDetailsModel->getCardDetails($listingID);
//            $itemName = $listingDetails["Name"];
//            $timeOfCreation = $listingDetails["Time_Of_Creation"];
//            $postCode = $listingDetails["Post_Code"];
//            $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);
//            $imageURL = $defaultImage["Image_URL"];
//            // Owner's details
//            $offeringUserID = $listingDetails["UserID"];
//            $offeringUserName = $listingDetails["Forename"] . " " . $listingDetails["Surname"];
//            $offeringUserImage = $this->cardDetailsModel->getUserImage($offeringUserID);
//            $item = array(
//                "transactionID" => $transactionID,
//                "startedDate" => $startedDate,
//                "userImg" => $offeringUserImage,
//                "userID" => $offeringUserID,
//                "userName" => $offeringUserName,
//                "addedDate" => $timeOfCreation,
//                "distance" => "CAN'T GET THIS INFORMATION",
//                "imgURL" => $imageURL,
//                "itemName" => $itemName,
//                "quantity" => $transactionQuantity,
//                "listingID" => $listingID,
//                "conversationID" => "SAME AS LISTING ID?",
//                "postCode" => $postCode
//            );
//            array_push($pendingRequests, $item);
//        }
//        $userListings = array("available" => $allAvailableListings, "outOfStock" => $allEmptyListings);
//        $offers = array("completed" => $completedOffers, "pending" => $pendingOffers);
//        $requests = array("completed" => $completedRequests, "pending" => $pendingRequests);
//
//        $listingsInformation = array(
//            "listingsCount" => $totalAvailabaleListings, // Total number of listings with quantity > 0
//            "emptyListingsCount" => $totalEmptyListings, // Total number of listings with quantity <= 0
//            "itemsOfferedCount" => $sendingTransactionsCount, // Total of all transactions for your items (can be greater than listings count)
//            "requestsMadeCount" => $receivingCount, // Total of all transactions you're in for other user's items
//            "userListings" => $userListings, // All your listings
//            "offers" => $offers, // Transactions for your items
//            "requests" => $requests // Transactions for other user's items
//        );
//        return $listingsInformation;
//    }

    /* Generates HTML for Home tab */
    function generateHomeSection()
    {
        //Get listings user has put up
        $userListingsSending = $this->model->getUserListings();

        // Total number of listings the user has offered
        // Should this be changed to not include completed listings?? (i.e 0 quantity)
        $listingsCount = count($userListingsSending);

        $sendingTransactionsCount = 0; // total number of transactions that have been made for user's listings
        $totalAvailabaleListings = 0; // total number of listings with quantity > 0
        $totalEmptyListings = 0; // total number of listings with 0 quantity

        //Counts number of transactions, available listings, and out-of-stock listings
        foreach ($userListingsSending as $listing) {
            $listingID = $listing["ListingID"];
            $listingQuantity = $listing["Quantity"]; // Will be 0 if listing has run out
            $active = $listing["Active"]; // Will be 0 if user no longer wants to see it
            // Only process active listings

            //Get details about the transactions involving this listing
            $stateDetails = $this->model->getStateOfListingTransaction($listingID);

            //Count number of relevant transactions
            if (count($stateDetails) > 0) {
                foreach ($stateDetails as $transaction) {
                    $completed = $transaction["Success"];
                    if ($completed != 2) {
                        $sendingTransactionsCount += 1;
                    }
                }
            }
            if ($active) {
                // Check whether it has quantity or not
                if ($listingQuantity > 0) {
                    $totalAvailabaleListings += 1;
                } else {
                    $totalEmptyListings += 1;
                }
            }
        }

        //Get listings user is receiving
        $userListingsReceiving = $this->model->getUserReceivingListings();

        // Total number of listings the user has requested
        // Should this be changed to not include completed listings?
        $receivingCount = 0;

        //Counts number of transactions
        foreach ($userListingsReceiving as $listing) {
            $completed = $listing["Success"]; // Transaction completed?
            if ($completed != 2) {
                $receivingCount += 1;
            }
        }

        // Get count for watch list tab
        $watchedListings = $this->model->getWatchedListings($this->getUserID());
        $watchListCount = count($watchedListings);

        // Get Recommendation HTML
        $recommendationHTML = $this->generateRecommendationHTML();

        $listingsInformation = array(
            "listingsCount" => $totalAvailabaleListings, // Total number of listings with quantity > 0
            "emptyListingsCount" => $totalEmptyListings, // Total number of listings with quantity <= 0
            "itemsOfferedCount" => $sendingTransactionsCount, // Total of all transactions for your items (can be greater than listings count)
            "requestsMadeCount" => $receivingCount, // Total of all transactions you're in for other user's items
            "watchListCount" => $watchListCount,
            "recommendationhtml" => $recommendationHTML
        );

        $template = $this->twig->loadTemplate("/users/homeTab.twig");
        return $template->render($listingsInformation);
    }


    /* Generates HTML for Offers tab (transactions for user's items)*/
    function generateOffersSection(){
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
                    $completed = $transaction["Success"];
                    if ($completed == 1) {
                        array_push($completedSending, $transactionID); //Get display information later
                    }elseif($completed == 0) {
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
            $imageURL = $defaultImage["Image_URL"];
            $item = array(
                "transactionID" => $transactionID,
                "completedDate" => $completedDate,
                "imgURL" => $imageURL,
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
            $imageURL = $defaultImage["Image_URL"];
            $item = array(
                "transactionID" => $transactionID,
                "startedDate" => $startedDate,
                "imgURL" => $imageURL,
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

        $listingsInformation = array(
            "offers" => $offers, // Transactions for your items
        );

        $template = $this->twig->loadTemplate("/users/offersTab.twig");
        return $template->render($listingsInformation);
    }


    /* Generates HTML for Requests tab*/
    function generateRequestsSection(){
        //Get listings user is receiving
        $userListingsReceiving = $this->model->getUserReceivingListings();
        $pendingReceiving = array();    //Incomplete transactions
        $completedReceiving = array();  //Incomplete transactions

        //Split listings into complete and pending transactions
        foreach ($userListingsReceiving as $listing) {
            $listingID = $listing["ListingID"];
            $transactionID = $listing["TransactionID"];
            $completed = $listing["Success"]; // Transaction completed?
            if ($completed == 1) {
                array_push($completedReceiving, $transactionID); //Get display information later
            } elseif($completed == 0) {
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
            $imageURL = $defaultImage["Image_URL"];
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
                "imgURL" => $imageURL,
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
            $imageURL = $defaultImage["Image_URL"];
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
                "imgURL" => $imageURL,
                "itemName" => $itemName,
                "quantity" => $transactionQuantity,
                "listingID" => $listingID,
                "conversationID" => "SAME AS LISTING ID?",
                "postCode" => $postCode
            );
            array_push($pendingRequests, $item);
        }

        $requests = array("completed" => $completedRequests, "pending" => $pendingRequests);

        $listingsInformation = array(
            "requests" => $requests // Transactions for other user's items
        );

        $template = $this->twig->loadTemplate("/users/requestsTab.twig");
        return $template->render($listingsInformation);
    }


    /* Generates HTML for Listings tab*/
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
            $imageURL = $defaultImage["Image_URL"];
            $isRequesting = $this->model->isRequesting($listingID, $this->getUserID());
            $isWatching = $this->inWatchList($listingID);
            $item = array(
                "listingID" => $listingID,
                "itemName" => $itemName,
                "addedDate" => $timeOfCreation,
                "quantity" => $quantity,
                "imgURL" => $imageURL,
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
            $imageURL = $defaultImage["Image_URL"];

            // Figure out whether to display "Request" or "Cancel request" button
            // $isRequesting = $this->cardDetailsModel->isUserRequestingListing($this->userID, $listingID);

            $item = array(
                "listingID" => $listingID,
                "itemName" => $itemName,
                "addedDate" => $timeOfCreation,
                "quantity" => $quantity,
                "imgURL" => $imageURL);
            array_push($allEmptyListings, $item);
        }

        $userListings = array("available" => $allAvailableListings, "outOfStock" => $allEmptyListings);
        $listingsInformation = array(
            "userListings" => $userListings, // All your listings
        );

        $template = $this->twig->loadTemplate("/users/listingsTab.twig");
        return $template->render($listingsInformation);
    }


    /* Generates the information for the watch list section (Generates HTML?)*/
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
            $distance = -1; // Can't get this information
            $postCode = $listing["Post_Code"];
            $addedDate = $details["Time_Of_Creation"];
            $defaultImage = $this->cardDetailsModel->getDefaultImage($listingID);
            $imageURL = $defaultImage["Image_URL"];
            // Owner's details
            $userID = $details["UserID"];
            $userImage = $this->cardDetailsModel->getUserImage($userID);
            $userName = $details["Forename"] . " " . $details["Surname"];

            $isRequesting = $this->model->isRequesting($listingID, $this->getUserID());
            $item = array(
                "listingID" => $listingID,
                "userImg" => $userImage,
                "userID" => $userID,
                "userName" => $userName,
                "addedDate" => $addedDate,
                "distance" => $distance, //CAN'T GET THIS INFORMATION
                "postCode" => $postCode,
                "imgURL" => $imageURL,
                "itemName" => $itemName,
                "isRequesting" => $isRequesting
            );
            array_push($watchList, $item);
        }

        $template = $this->twig->loadTemplate("/users/watchlistTab.twig");
        return $template->render(array("watchList"=>$watchList));

        //return $watchListDetails;
    }


    /* Generates HTML for recommendation section */
    function generateRecommendationHTML(){
        $controller = new RecommendationController();
        return $controller->generateRecommendedSection();
    }


    /* Returns true if $listingID is in the current user's watch list*/
    function inWatchList($listingID){
        $watchedListings = $this->model->getWatchedListings($this->getUserID());
        $inWatchList = False; // Assume it isn't in the watch list
        foreach($watchedListings as $listing){
            if($listing["ListingID"] == $listingID){
                $inWatchList = True;
                break;
            }
        }
        return $inWatchList;
    }


    /* Either adds or deletes a listing from the current user's watch list */
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
}