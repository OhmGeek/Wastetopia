<?php
namespace Wastetopia\Controller;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Wastetopia\Model\AnalysisModel;
use Wastetopia\Controller\SearchController;  
use Wastetopia\Config\CurrentConfig;

class RecommendationController {
    
    function __construct(){
      $this->model = new AnalysisModel();
      
      $loader = new Twig_Loader_Filesystem('../view/');
      $this->twig = new Twig_Environment($loader);
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
          $userImage = "NOPE";//$listing[""]; // Needs adding
          $userID = $listing["UserID"];
          $userName = $listing["Forename"]." ".$listing["Surname"];
          $addedDate = $listing["Time_Of_Creation"];
          $distance = "DON'T HAVE"; // May be able to add later
          $postCode = $listing["Post_Code"];
          $imgURL = "NOPE";//$listing[""]; // Add later
          $itemName = $listing["Name"];
          $quantity = $listing["Quantity"];
          
          $item = array(
            "listingID" => $listingID,
            "userImg" => $userImage,
            "userID" => $userID,
            "userName" => $userName,
            "addedDate" => $addedDate,
            "postCode" => $postCode,
            "imgURL" => $imgURL,
            "itemName" => $itemName,
            "quantity" => $quantity
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
}

