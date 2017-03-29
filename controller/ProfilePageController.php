<?php
/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 03/03/2017
 * Time: 11:24
 */
namespace Wastetopia\Controller;

use Wastetopia\Model\ProfilePageModel;
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
        return 6;
    }


    function generatePage()
    {
        $userInformation = $this->generateProfileSection();
        $listingsInformation = $this->generateListingsSection();
        $watchListDetails = $this->generateWatchListSection();

        $isCurrentUser = ($this->userID == $this->getUserID() ? 1 : 0); // 1 if logged in user trying to view their own profile
        
        $output = array(
            "isUser" => $isCurrentUser,
            
            "userimage" => $userInformation["userimage"],
            "username" => $userInformation["username"],
            "userscore" => $userInformation["userscore"],

            "listingsCount" => $listingsInformation["listingsCount"],
            "itemsOfferedCount" => $listingsInformation["sendingTransactionsCount"],
            "requestsMadeCount" => $listingsInformation["receivingCount"],
            "userListings" => $listingsInformation["allUserListings"],
            "offers" => $listingsInformation["offers"],
            "requests" => $listingsInformation["requests"],

            "watchListCount" => $watchListDetails["watchListCount"],
            "watchList" => $watchListDetails["watchList"]
        );
        
        $template = $this->twig->loadTemplate('users/profile.twig');
        return $template->render($output);
    }


    /* Generates the profile information for the website */
    function generateProfileSection()
    {
        //Get user details
        $userDetails = $this->model->getUserDetails();
        $userImage = $this->model->getUserImage($this->userID);
        $userInformation = array();
        $userInformation["username"] = $userDetails["Forename"] . " " . $userDetails["Surname"];
//        $userInformation["email"] = $userDetails["Email_Address"];
        $userInformation["userscore"] = $userDetails["Mean_Rating_Percent"];
        $userInformation["userimage"] = $userImage;
        return $userInformation;
    }


    /* Generates the information for the Listings section of the page
    TODO: Sort out difference between a transaction and a listing
    TODO: Show users transactions for pending and completed with the quantities from ListingTransaction table */
    function generateListingsSection()
    {
        //Get listings user has put up
        $userListingsSending = $this->model->getUserListings();

        $pendingSending = array();    //Incomplete transactions (List of TransactionIDs)
        $completedSending = array();  //Complete transactions (List of TransactionIDs
        $allListingsSending = array(); // List of all listingIDs user is giving away

        // Total number of listings the user has offered
        // Should this be changed to not include completed listings?? (i.e 0 quantity)
        $listingsCount = count($userListingsSending);

        // total number of transactions that have been made for user's listings
        $sendingTransactionsCount = 0;

        //Split listings into complete and pending transactions (and listings with no transactions)
        foreach ($userListingsSending as $listing) {
            $listingID = $listing["ListingID"];
            array_push($allListingsSending, $listingID); //Add to full list

            //Get details about the transactions involving this listing
            $stateDetails = $this->model->getStateOfListingTransaction($listingID);

            //If no transactions, this listing will not be in the history page
            if (count($stateDetails) > 0) {
                foreach ($stateDetails as $transaction) {
                    $sendingTransactionsCount += 1;
                    $transactionID = $transaction["TransactionID"];
                    $completed = $transaction["Success"];
                    if ($completed) {
                        // Need to figure out how to deal with these as transactions
                        array_push($completedSending, $transactionID); //Get display information later
                    } else {
                        // Need to figure out how to deal with these as transactions
                        array_push($pendingSending, $transactionID);   //Get display information later
                    }
                }
            }
        }

        //Get listings user is receiving
        $userListingsReceiving = $this->model->getUserReceivingListings();
        $pendingReceiving = array();    //Incomplete transactions
        $completedReceiving = array();  //Incomplete transactions

        // Total number of listings the user has requested
        // Should this be changed to not include completed listings?
        $receivingCount = count($userListingsReceiving);

        //Split listings into complete and pending transactions
        foreach ($userListingsReceiving as $listing) {
            $listingID = $listing["ListingID"];
            $transactionID = $listing["TransactionID"];
            $completed = $listing["Success"]; // Transaction completed?

            if ($completed) {
                // Need to figure out how to deal with these as transactions
                array_push($completedReceiving, $transactionID); //Get display information later
            } else {
                // Need to figure out how to deal with these as transactions
                array_push($pendingReceiving, $transactionID);   //Get display information later
            }
            }

        // NOW GET APPROPRIATE INFORMATION FROM EACH LISTING

        $allUserListings = array(); // All listings the user has put up

        $completedOffers = array();
        $pendingOffers = array();

        $completedRequests = array();
        $pendingRequests = array();

        // Get information for all user's listings
        foreach ($allListingsSending as $listingID) {
            $details = $this->model->getCardDetails($listingID);
//            $itemID = $details["ItemID"];
            $itemName = $details["Name"];
            $timeOfCreation = $details["Time_Of_Creation"];
            $defaultImage = $this->model->getDefaultImage($listingID);
            $imageURL = $defaultImage["Image_URL"];
            $item = array(
                "listingID" => $listingID,
                "itemName" => $itemName,
                "addedDate" => $timeOfCreation,
                "imgURL" => $imageURL);
            array_push($allUserListings, $item);
        }

        //NEXT TWO FOR LOOPS ARE ALMOST IDENTICAL, CHANGE NAMES IN TWIG SO THESE CAN BE MADE INTO ONE FUNCTION

        // Completed sending transactions
        foreach($completedSending as $transactionID){
            $transactionDetails = $this->model->getDetailsFromTransactionID($transactionID);
            // Info specific to transaction
            $transactionQuantity = $transactionDetails["Quantity"];
            $completedDate = $transactionDetails["Time_Of_Transaction"];

            //In for about user involved in this transaction
            $requestingUserID = $transactionDetails["UserID"];
            $requestingUserName = $transactionDetails["Forename"]." ".$transactionDetails["Surname"];
            $requestingUserImage = $this->model->getUserImage($requestingUserID);

            // Information about the item/listing involved
            $listingID = $transactionDetails["ListingID"];
            $listingDetails = $this->model->getCardDetails($listingID);
            $itemName = $listingDetails["Name"];
            $defaultImage = $this->model->getDefaultImage($listingID);
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
        foreach($pendingSending as $transactionID){
            $transactionDetails = $this->model->getDetailsFromTransactionID($transactionID);
            // Info specific to transaction
            $transactionQuantity = $transactionDetails["Quantity"];
            $startedDate = $transactionDetails["Time_Of_Transaction"];

            //In for about user involved in this transaction
            $requestingUserID = $transactionDetails["UserID"];
            $requestingUserName = $transactionDetails["Forename"]." ".$transactionDetails["Surname"];
            $requestingUserImage = $this->model->getUserImage($requestingUserID);

            // Information about the item/listing involved
            $listingID = $transactionDetails["ListingID"];
            $listingDetails = $this->model->getCardDetails($listingID);
            $itemName = $listingDetails["Name"];
            $defaultImage = $this->model->getDefaultImage($listingID);
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

        // Completed requesting transactions
        foreach($completedReceiving as $transactionID){
            $transactionDetails = $this->model->getDetailsFromTransactionID($transactionID);
            // Info specific to transaction
            $transactionQuantity = $transactionDetails["Quantity"];
            $completedDate = $transactionDetails["Time_Of_Transaction"];


            // Information about the item/listing involved
            $listingID = $transactionDetails["ListingID"];
            $listingDetails = $this->model->getCardDetails($listingID);
            $itemName = $listingDetails["Name"];
            $timeOfCreation = $listingDetails["Time_Of_Creation"];
            $defaultImage = $this->model->getDefaultImage($listingID);
            $imageURL = $defaultImage["Image_URL"];

            // Owner's details
            $offeringUserID = $listingDetails["UserID"];
            $offeringUserName = $listingDetails["Forename"]." ".$listingDetails["Surname"];
            $offeringUserImage = $this->model->getUserImage($offeringUserID);

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
                "listingID" => $listingID
            );

            array_push($completedRequests, $item);
        }

        // Pending requesting transactions
        foreach($pendingReceiving as $transactionID){
            $transactionDetails = $this->model->getDetailsFromTransactionID($transactionID);

            // Info specific to transaction
            $transactionQuantity = $transactionDetails["Quantity"];
            $startedDate = $transactionDetails["Time_Of_Transaction"];


            // Information about the item/listing involved
            $listingID = $transactionDetails["ListingID"];
            $listingDetails = $this->model->getCardDetails($listingID);
            $itemName = $listingDetails["Name"];
            $timeOfCreation = $listingDetails["Time_Of_Creation"];
            $defaultImage = $this->model->getDefaultImage($listingID);
            $imageURL = $defaultImage["Image_URL"];

            // Owner's details
            $offeringUserID = $listingDetails["UserID"];
            $offeringUserName = $listingDetails["Forename"]." ".$listingDetails["Surname"];
            $offeringUserImage = $this->model->getUserImage($offeringUserID);

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
                "conversationID" => "SAME AS LISTING ID?"
            );

            array_push($pendingRequests, $item);
        }

        $offers = array("completed" => $completedOffers, "pending" => $pendingOffers);
        $requests = array("completed" => $completedRequests, "pending" => $pendingRequests);

        $listingsInformation = array(
            "listingsCount" => $listingsCount, // Total currently includes all listings that have quantity of 0
            "itemsOfferedCount" => $sendingTransactionsCount, // Total of all transactions for your items (can be greater than listings count)
            "requestsMadeCount" => $receivingCount, // Total of all transactions you're in for other user's items
            "userListings" => $allUserListings, // All your listings
            "offers" => $offers, // Transactions for your items
            "requests" => $requests // Transactions for other user's items
        );

        return $listingsInformation;
    }


    /* Generates the information for the watch list section */
    function generateWatchListSection()
    {
        //Get IDs of listings user is watching
        $watchedListings = $this->model->getWatchedListings();

        $count = count($watchedListings);

        $watchList = array();
        foreach ($watchedListings as $listing) {
            $watchID = $listing["WatchID"];
            $listingID = $listing["ListingID"];

            // Get details about the listing
            $details = $this->model->getCardDetails($listingID);
            $itemName = $details["Name"];
            $distance = -1; // Can't get this information
            $addedDate = $details["Time_Of_Creation"];
            $defaultImage = $this->model->getDefaultImage($listingID);
            $imageURL = $defaultImage["Image_URL"];

            // Owner's details
            $userID = $details["UserID"];
            $userImage = $this->model->getUserImage($userID);
            $userName = $details["Forename"] . " " . $details["Surname"];

            $item = array(
                "listingID" => $listingID,
                "userImg" => $userImage,
                "userID" => $userID,
                "userName" => $userName,
                "addedDate" => $addedDate,
                "distance" => $distance, //CAN'T GET THIS INFORMATION
                "imgURL" => $imageURL,
                "itemName" => $itemName,
                );

            array_push($watchList, $item);
        }

        $watchListDetails = array(
            "watchListCount" => $count,
            "watchList" => $watchList
        );

        return $watchListDetails;
    }
}
