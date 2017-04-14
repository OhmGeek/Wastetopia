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
     * Returns the ID of the user currently logged in
     * @return int
     */
    private function getUserID()
    {
        return 6; // Hard coded for now
    }
    
    
    /**
    * Generates the HTML for the cards in a recommended section
    */
    function generateRecommendedSection(){
      $frequentTags = $this->model->getTagFrequenciesForTransactions();

      print_r($frequentTags);

      // Deal with if there are not enough tags    
      if(count($frequentTags) < 3){
          $recommendationList = array(); // Empty array
      }else{        
          // Extract 5 most frequent tags
          $tags = array();

          for($x = 0; $x < 5; $x++){
              $tagDetails = $frequentTags[$x];
              $tagID = $tagDetails["TagID"];
              array_push($tags, $tagID);
          }
//           print_r("RECOMMENDATIONS");
//           print_r($tags);
          // Use search query using $tags to find listings that match these tags
          // Get user's lat/long 
          // get $results
          $searchController = new SearchController();
          $results = $searchController->recommendationSearch($tags, $this->getUserID());
          

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
              $isRequesting = $this->profilePageModel->isRequesting($listingID, $this->getUserID());
              $isWatching = $this->profilePageModel->isWatching($listingID, $this->getUserID());
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
      }
        
      $currentConfig = new CurrentConfig();
      $config = $currentConfig->getAll();  
        
      $output = array(
            "config" => $config,
            "section" => "recommendation", 
            "recommendationList" => $recommendationList
      );
        
        $template = $this->twig->loadTemplate('/items/recommendations.twig');
      
        return $template->render($output); // Render with cardDetails for listings that match most frequent tags
      }
    
       
      /**
    * Generates the HTML for the cards in a prediction of similar items you may give away section
    */
    function generatePredictionSection(){
      $frequentTags = $this->model->getTagFrequenciesForListings();
      
        // Deal with if there are not enough tags    
      if(count($frequentTags) < 5){
          $recommendationList = array(); // Empty array
      }else{   
          // Extract 5 most frequent tags
          $tags = array();

          for($x = 0; $x < 5; $x++){
              $tagDetails = $frequentTags[$x];
              $tagID = $tagDetails["TagID"];
              array_push($tags, $tagID);
          }
//           print_r("PREDICTIONS");
//             print_r($tags);
          // Use search query using $tags to find listings that match these tags
          // Get user's lat/long 
          // get $results
          $searchController = new SearchController();
          $results = $searchController->recommendationSearch($tags, $this->getUserID());
            
//           print_r("Search results: ");
//           print_r($results);
          
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
              $isRequesting = $this->profilePageModel->isRequesting($listingID, $this->getUserID());
              $isWatching = $this->profilePageModel->isWatching($listingID, $this->getUserID());
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
      }
        
      $currentConfig = new CurrentConfig();
      $config = $currentConfig->getAll();  
        
      $output = array(
            "config" => $config,
            "section" => "prediction",  
            "recommendationList" => $recommendationList
      );
        
        $template = $this->twig->loadTemplate('/items/recommendations.twig');
      
        return $template->render($output); // Render with cardDetails for listings that match most frequent tags
      }


// NEED TO FINISH THESE TWO PREDICTION/ADVICE FUNCTIONS    
    /**
    * Generates a list of the top 5 names that appear in items user gives away
    */
    function generatePredictionFromName(){
        // Get itemNames along with frequencies of occurence in items user gives away
        $nameFrequencies = $this->model->getTotalNameFrequenciesSending();
        
        $topGiven = array(); // Array of top 5 names of items user gives away
        
        for ($x = 0; $x < 5; $x++){
            $itemDetails = $nameFrequencies[$x];  
            $itemName = $itemDetails["Name"];
            array_push($topGiven, $itemName);
        }
        
    }


    /**
    * Generates a bit of advice based on the top 5 most frequent Type tags are found on items user gives away
    */
    function generateAdviceFromTagsGiven(){
        $frequentTags = $this->model->getTagFrequenciesForListings(array(1)); // 1 - only looks for type
      
        // Deal with if there are not enough tags    
      if(count($frequentTags) < 5){
          $recommendationList = array(); // Empty array
      }else{   
          // Extract 5 most frequent tags
          $tags = array();
          
          for($x = 0; $x < 5; $x++){
              $tagDetails = $frequentTags[$x];
              $tagID = $tagDetails["TagID"];
              array_push($tags, $tagID);
          }
          
          // Do something with these to give some advice on what to stop buying
          
      }        
    }
}

