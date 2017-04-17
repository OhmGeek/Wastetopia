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
    }

    function renderIndexPage()
    {
        $loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

        $controller = new SearchPageController();

        $currentConfig = new CurrentConfig();
        $config = $currentConfig->getAll();

        $filters = $controller->getSearchFilters();

        $template = $twig->loadTemplate("index.twig");
        return $template->render(array('config' => $config,
                                       'filters' => $filters
                                     ));
    }
  }
?>
