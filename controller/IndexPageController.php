<?php

namespace Wastetopia\Controller;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Wastetopia\Config\CurrentConfig;
use Wastetopia\Controller\Authenticator;
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
        return Authenticator::isAuthenticated();
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

        $loggedMessage = $this->generateRandomMessage();

        // Generate HTML
        return $template->render(array('config' => $config,
                                       'isLoggedIn' => $isLoggedIn,
                                       'loggedMessage' => $loggedMessage,
                                       'filters' => $filters
                                     ));
    }

    function generateRandomMessage() {
    	$messages = array(
    		"Designed by keen beans with ❤",
    		"Fooooooooooooooooooood! 😀",
    		"Designed by a bunch of old Eat-onions",
    		"Are you bready to find some food?",
    		"This site isn't rubbish, it's here to prevent rubbish!",
    		"/usr/bin/make food <br> make: *** No rule to make target 'food'. Stop."
    	);
    	$rand_keys = array_rand($messages, 1);
    	return $messages[$rand_keys[0]];
    }
  }
?>
