<?php

class SearchPageController
{
    function __construct()
    {
        $this->createPage();
    }

    function createPage()
    {
        $loader = new Twig_Loader_Filesystem('../../view/');
        $twig = new Twig_Environment($loader);

        $template = $twig->loadTemplate("searchPage.twig");
        print($template->render(array()));
    }
}