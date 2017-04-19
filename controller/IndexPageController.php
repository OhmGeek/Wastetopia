<?php

namespace Wastetopia\Controller;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Wastetopia\Config\CurrentConfig;
use Wastetopia\Controller\SearchPageController;

class IndexPageController
{
    function __construct()
    {
        // New instance of SearchPageController
        $this->controller = new SearchPageController();
        
        // Set up twig stuff
        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);

    }

    /**
     * Returns True if getUserID doesn't return "" or null
     * @return bool True if user is logged in
     */
    function isUserLoggedIn(){
        return \Wastetopia\Controller\Authenticator::isAuthenticated();
    }
    
    /**
    * Generates HTML for the main home page
    * @return HTML
    */
    function renderIndexPage()
    {
        
        $currentConfig = new CurrentConfig();
        $config = $currentConfig->getAll();

        // Get possible search filters 
        $filters = $this->controller->getSearchFilters();

        // Load template
        $template = $this->twig->loadTemplate("index.twig");
        
        $isLoggedIn = $this->isUserLoggedIn();

        // Generate HTML
        return $template->render(array('config' => $config,
                                       'isLoggedIn' => $isLoggedIn,
                                       'filters' => $filters
                                     ));
    }
  }
?>
