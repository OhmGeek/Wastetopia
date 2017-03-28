<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 03/03/2017
 * Time: 11:24
 */
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
        
    }
    
    /* Generates the profile information HTML for the website */
    function generateProfileSection(){
        //Get user details
        $userDetails = $this->model->getUserDetails();

        $user = array();
        $user["forename"] = $userDetails["Forename"];
        $user["surname"] = $userDetails["Surname"];
        $user["email"] = $userDetails["Email_Address"];
        //$user["popularityRating"] = $userDetails["Mean_Rating_Percent"];

        //Get ProfilePage twig file and display it
        $template = $this->twig->loadTemplate("TWIG_FILE");
        print($template->render(array("user"=>$user)));
    }
    
    
    /* Generates the HTML for the Listings section of the page */
    function generateListingsSection(){
        //Get listings user has put up
        $userListingsSending = $this->model->getUserListings();
        $noInterestSending = array(); //No transactions (list of ListingIDs)
        $pendingSending = array();    //Incomplete transactions (List of TransactionIDs)
        $completedSending = array();  //Complete transactions (List of TransactionIDs
        
        //Split listings into complete and pending transactions (and listings with no transactions)
        foreach ($userListingsSending as $listing){
            $listingID = $listing["ListingID"];
            $stateDetails = $this->model->getStateOfListingTransaction($listingID); //Get details about the transactions
            //If no transactions, this listing will not be in the history page
            if (count($stateDetails) > 0){
                foreach ($stateDetails as $transaction){
                    $transactionID = $transaction["TransactionID"];
                    $completed = $transaction["Sucess"];
                    if ($completed){
                        array_push($completedSending, $transactionID); //Get display information later
                    }else{
                        array_push($pendingSending, $transactionID);   //Get display information later
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
        
        //Split listings into complete and pending transactions
        foreach ($userListingsSending as $listing){
            $listingID = $listing["ListingID"];
            $stateDetails = $this->model->getStateOfListingTransaction($listingID); //Get details about the transactions
            foreach ($stateDetails as $transaction){
                $transactionID = $transaction["TransactionID"];
                $completed = $transaction["Sucess"];
                if ($completed){
                    array_push($completedReceiving, $transactionID); //Get display information later
                }else{
                    array_push($pendingReceiving, $transactionID);   //Get display information later
                }
            }
        }
        
        //Loop through arrays, getting display information (from SearchController???)
        //Add these details to arrays
        //Render arrays in output
        //Get HistoryPage twig file and display it
        $template = $this->twig->loadTemplate("TWIG_FILE");
        print($template->render());
    }
    
    
    /* Generates the HTML for the watch list section */
    function generateWatchListSection(){
        //Get IDs of listings user is watching
        $watchedListings = $this->model->getWatchedListings();
        $watchList = array();
        foreach($watchedListings as $listing){
            $watchItem = array();
            $watchItem["WatchID"] = $listing["WatchID"]; //WatchID to keep track of it
            $listingID = $listing["ListingID"];
            //Use SearchModel?? to get details from listingID
            //Add these details to $watchItem
            array_push($watchList, $watchItem);
        }
        //Get ProfilePage twig file and display it
        $template = $this->twig->loadTemplate("TWIG_FILE");
        print($template->render(array("watchList"=>$watchList)));
    }


}
