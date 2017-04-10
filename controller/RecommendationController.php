<?php
namespace Wastetopia\Controller;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Wastetopia\Model\AnalysisModel;
use Wastetopia\Controller\SearchController;  
use Wastetopia\Config\CurrentConfig;
use Wastetopia\Model\CardDetailsModel;
use Wastetopia\Model\ProfilePageModel;

class RecommendationController {
    
    function __construct(){
      $this->model = new AnalysisModel();
      $this->cardDetailsModel = new CardDetailsModel();  
      $this->profilePageModel = new ProfilePageModel($this->getUserID());
        
      $loader = new Twig_Loader_Filesystem('../view/');
      $this->twig = new Twig_Environment($loader);
    }
    
    /**
     * Returns the ID of the user whose profile you're trying to view
     * @return int
     */
    private function getUserID()
    {
        return $this->userID;
    }
    
    
    /**
    * Generates the HTML for the cards in a recommended section
    */
    function generateRecommendedSection(){
      $frequentTags = $this->model->getTagFrequenciesForTransactions();
      
      // Deal with if there are not enough tags
      
      // Extract 5 most frequent tags
      $tags = array();
      
      for($x = 0; $x < 5; $x++){
          $tagDetails = $frequentTags[x];
          $tagID = $tagDetails["TagID"];
          array_push($tags, $tagID);
      }
      
      // Use search query using $tags to find listings that match these tags
      // Get user's lat/long 
      // get $results
      $searchController = new SearchController();
      $results = $searchController->recommendationSearch($tags);
        
      $recommendationList = array();  
      foreach($results as $listing){
          $listingID = $listing["ListingID"];
          $userID = $listing["UserID"];
          $userImage = $this->cardDetailsModel->getUserImage($userID);//$listing[""]; // Needs adding
          $userName = $listing["Forename"]." ".$listing["Surname"];
          $addedDate = $listing["Time_Of_Creation"];
          $distance = "DON'T HAVE"; // May be able to add later
          $postCode = $listing["Post_Code"];
          $imgURL = $this->cardDetailsModel->getDefaultImage($listingID);//$listing[""]; // Add later
          $itemName = $listing["Name"];
          $quantity = $listing["Quantity"];
          $isRequesting = $this->profilePageModel->isRequesting($listingID);
          $isWatching = $this->inWatchList($listingID);
          $item = array(
            "listingID" => $listingID,
            "userImg" => $userImage,
            "userID" => $userID,
            "userName" => $userName,
            "addedDate" => $addedDate,
            "postCode" => $postCode,
            "imgURL" => $imgURL,
            "itemName" => $itemName,
            "quantity" => $quantity,
            "isRequesting" => $isRequesting,
            "isWatching" => $isWatching  
          );
          
          array_push($recommendationList, $item);
      }
        
      $currentConfig = new CurrentConfig();
      $config = $currentConfig->getAll();  
        
      $output = array(
            "config" => $config,
            "recommendationList" => $recommendationList
      );
        
        $template = $this->twig->loadTemplate('/items/recommendations.twig');
      
        return $template->render($output); // Render with cardDetails for listings that match most frequent tags
      }
    
    /**
    * Returns True if the given listing is in the current user's watch list
    * @param $listingID
    * @returns boolean
    */
    function inWatchList($listingID){
        $watchedListings = $this->profilePageModel->getWatchedListings();
        $inWathcList = False; // Assume it isn't in the watch list
        foreach($watchedListings as $listing){
            if($listing["ListingID"] == $listingID){
                $inWatchList = True;
                break;
            }
        }
        return $inWatchList;
    }
}

