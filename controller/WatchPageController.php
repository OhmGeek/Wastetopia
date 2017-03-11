<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 04/03/2017
 * Time: 12:17
 */
class WatchPageController
{

    public function __construct()
    {
        $this->model = new ProfilePageModel(); //Need to include

        //Load Twig environment
        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);
    }


    function generatePage()
    {
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