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
     */
    public function __construct()
    {
        $this->model = new ProfilePageModel(); //Need to include
        //Load Twig environment
        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);
    }
    function generatePage()
    {
        $userInformation = $this->generateProfileSection();
        $listingsInformation = $this->generateListingsSection();
        $watchListDetails = $this->generateWatchListSection();

        $output = array(
            "username" => $userInformation["username"],
            "userscore" => $userInformation["userscore"],

            "itemsOfferedCount" => $listingsInformation["itemsOfferedCount"],
            "requestsMadeCount" => $listingsInformation["requestsMadeCount"],
            "offers" => $listingsInformation["offers"],
            "requestsMade" => $listingsInformation["requestsMade"],

            "watchlistCount" => $watchListDetails["watchListCount"],
            "watchList" => $watchListDetails["watchList"]
        );


    }

    /* Generates the profile information for the website */
    function generateProfileSection(){
        //Get user details
        $userDetails = $this->model->getUserDetails();
        $userInformation = array();
        $userInformation["username"] = $userDetails["Forename"]." ".$userDetails["Surname"];
        $userInformation["email"] = $userDetails["Email_Address"];
        $userInformation["userscore"] = $userDetails["Mean_Rating_Percent"];

        return $userInformation;
    }


    /* Generates the information for the Listings section of the page
    TODO: Sort out difference between a transaction and a listing
    TODO: Show users transactions for pending and completed with the quantities from ListingTransaction table */
    function generateListingsSection(){
        //Get listings user has put up
        $userListingsSending = $this->model->getUserListings();
        $noInterestSending = array(); //No transactions (list of ListingIDs)
        $pendingSending = array();    //Incomplete transactions (List of TransactionIDs)
        $completedSending = array();  //Complete transactions (List of TransactionIDs

        // Total number of listings the user has offered
        // Should this be changed to not include completed listings?
        $sendingCount = count($userListingsSending);


        $allListingsSending = array(); //Added for testing


        //Split listings into complete and pending transactions (and listings with no transactions)
        foreach ($userListingsSending as $listing){
            $listingID = $listing["ListingID"];

            array_push($allListingsSending, $listingID); //Added for testing

            $stateDetails = $this->model->getStateOfListingTransaction($listingID); //Get details about the transactions
            //If no transactions, this listing will not be in the history page
            if (count($stateDetails) > 0){
                foreach ($stateDetails as $transaction){
                    $transactionID = $transaction["TransactionID"];
                    $completed = $transaction["Sucess"];
                    if ($completed){
                        // Need to figure out how to deal with these as transactions
                        array_push($completedSending, $listingID); //Get display information later
                    }else{
                        // Need to figure out how to deal with these as transactions
                        array_push($pendingSending, $listingID);   //Get display information later
                    }
                }
            }else{
                array_push($noInterestSending, $listingID);            //Get display info later
            }
        }


        //Get listings user is receiving
        $userListingsReceiving = $this->model->getUserReceivingListings();
        $pendingReceiving = array();    //Incomplete transactions
        $completedReceiving = array();  //Incomplete transactions

        // Total number of listings the user has requested
        // Should this be changed to not include completed listings?
        $receivingCount = count($userListingsReceiving);

        $allListingsReceiving = array(); //Added for testing

        //Split listings into complete and pending transactions
        foreach ($userListingsReceiving as $listing){
            $listingID = $listing["ListingID"];

            array_push($allListingsReceiving, $listingID); //Added for testing

            $stateDetails = $this->model->getStateOfListingTransaction($listingID); //Get details about the transactions
            foreach ($stateDetails as $transaction){
                $transactionID = $transaction["TransactionID"];
                $completed = $transaction["Sucess"];
                if ($completed){
                    // Need to figure out how to deal with these as transactions
                    array_push($completedReceiving, $listingID); //Get display information later
                }else{
                    // Need to figure out how to deal with these as transactions
                    array_push($pendingReceiving, $listingID);   //Get display information later
                }
            }
        }

        $offers = array();
        $requestsMade = array();
        foreach($allListingsSending as $listingID){
            $details = $this->model->getCardDetails($listingID);
            $itemName = $details["Name"];
            $defaultImage = $this->model->getDefaultImage($listingID);
            $imageURL = $defaultImage["Image_URL"];
            $item = array("itemName" => $itemName, "imgURL" => $imageURL);
            array_push($offers, $item);
        }

        foreach($allListingsReceiving as $listingID){
            $details = $this->model->getCardDetails($listingID);
            $itemName = $details["Name"];
            $distanceToUser = -1; // Can't get this information
            $dateAdded = $details["Time_Of_Creation"];
            $username = $details["Forename"]." ".$details["Surname"];

            $defaultImage = $this->model->getDefaultImage($listingID);
            $imageURL = $defaultImage["Image_URL"];

            $item = array(
                "username" => $username,
                "dateAdded" => $dateAdded,
                "distanceToUser" => $distanceToUser,
                "itemName" => $itemName,
                "imgURL" => $imageURL);
            array_push($requestsMade, $item);
        }

        $listingsInformation = array(
            "itemsOfferedCount" => $sendingCount,
            "requestsMadeCount" => $receivingCount,
            "offers" => $offers,
            "requestsMade" => $requestsMade
        );
    }


    /* Generates the information for the watch list section */
    function generateWatchListSection(){
        //Get IDs of listings user is watching
        $watchedListings = $this->model->getWatchedListings();

        $count = count($watchedListings);

        $watchList = array();

        foreach($watchedListings as $listing){
            $watchID = $listing["WatchID"];
            $listingID = $listing["ListingID"];

            $details = $this->model->getCardDetails($listingID);
            $itemName = $details["Name"];
            $distanceToUser = -1; // Can't get this information
            $dateAdded = $details["Time_Of_Creation"];
            $username = $details["Forename"]." ".$details["Surname"];

            $defaultImage = $this->model->getDefaultImage($listingID);
            $imageURL = $defaultImage["Image_URL"];

            $item = array(
                "watchID" => $watchID,
                "username" => $username,
                "dateAdded" => $dateAdded,
                "distanceToUser" => $distanceToUser,
                "itemName" => $itemName,
                "imgURL" => $imageURL);
            array_push($watchList, $item);
        }

        $watchListDetails = array(
            "watchlistCount" => $count,
            "watchList" => $watchList
        );
    }
}