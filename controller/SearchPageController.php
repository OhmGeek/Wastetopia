<?php

namespace Wastetopia\Controller;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Wastetopia\Config\CurrentConfig;

class SearchPageController
{
    function __construct()
    {
    }

    function render()
    {
        $loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);


        $currentConfig = new CurrentConfig();
        $config = $currentConfig->getAll();

        $template = $twig->loadTemplate("search/search.twig");
        return $template->render(array('config' => $config
            
                                      ));
    }
}