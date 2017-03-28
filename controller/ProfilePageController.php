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
     * @param ID of user whose profile you wish to view
     */
    public function __construct($userID)
    {
        $this->model = new ProfilePageModel($userID); //Need to include
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
        
  
     
       // print_r($watchListDetails);

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

        $template = $this->twig->loadTemplate('users/profile.twig');

        //return $template->render($output);

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

        // total number of transactions that have been made for user's listings
        $sendingTransactionsCount = 0;

        $allListingsSending = array(); //Added for testing


        //Split listings into complete and pending transactions (and listings with no transactions)
        foreach ($userListingsSending as $listing){
            $listingID = $listing["ListingID"];

            array_push($allListingsSending, $listingID); //Added for testing

            $stateDetails = $this->model->getStateOfListingTransaction($listingID); //Get details about the transactions
            //If no transactions, this listing will not be in the history page
            if (count($stateDetails) > 0){
                foreach ($stateDetails as $transaction){
                    $sendingTransactionsCount += 1;
                    $transactionID = $transaction["TransactionID"];
                    $completed = $transaction["Success"];
                    if ($completed){
                        // Need to figure out how to deal with these as transactions
                        array_push($completedSending, $listingID); //Get display information later
                    }else{
                        // Need to figure out how to deal with these as transactions
                        array_push($pendingSending, $listingID);   //Get display information later
                    }
                }
            }
//            else{
//                array_push($noInterestSending, $listingID);            //Get display info later
//            }
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
                $completed = $transaction["Success"];
                if ($completed){
                    // Need to figure out how to deal with these as transactions
                    array_push($completedReceiving, $listingID); //Get display information later
                }else{
                    // Need to figure out how to deal with these as transactions
                    array_push($pendingReceiving, $listingID);   //Get display information later
                }
            }
        }

        // DONE FOR TESTING
        // WILL SPLIT INTO LISTINGS AND PENDING/COMPLETED TRANSACTIONS
        $offers = array();
        $requestsMade = array();
        print_r("Sending: ");
        print_r($allListingsSending);
        print_r($this->model->getCardDetails(10));
        foreach($allListingsSending as $listingID){
            $details = $this->model->getCardDetails($listingID);
            print_r($details);
            $itemName = $details["Name"];
            $defaultImage = $this->model->getDefaultImage($listingID);
            $imageURL = $defaultImage["Image_URL"];
            $item = array("listingID"=>$listingID, "itemName" => $itemName, "imgURL" => $imageURL);
            print_r($item);
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
                "listingID"=>$listingID,
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
        
        return $listingsInformation;
    }


    /* Generates the information for the watch list section */
    function generateWatchListSection(){
        //Get IDs of listings user is watching
        $watchedListings = $this->model->getWatchedListings();

        print_r("Watched listings:: ");
        print_r($watchedListings);
        
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
                "listingID" => $listingID,
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
        
        return $watchListDetails;
    }
}
