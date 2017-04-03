<?php
namespace Wastetopia\Controller;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Wastetopia\Model\AnalysisModel

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
      
      $template = $this->twig->loadTemplate('DOESN'T EXIST');
      
      return $template->render($results); // Render with cardDetails for listings that match most frequent tags
      }
    }
}
