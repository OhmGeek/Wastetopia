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
        $controller = new SearchPageController();
        
        // Set up twig stuff
        $loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

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

        // Generate HTML
        return $template->render(array('config' => $config,
                                       'filters' => $filters
                                     ));
    }
  }
?>
