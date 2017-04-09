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

        $filters = array(array('id'=>'0', 'optionsCategory'=>'Allergens', 'options' => array(array('value' => 'Nuts', 'id' => '1'),
                                                                                             array('value' => 'Gluten', 'id' => '2'),
                                                                                             array('value' => 'Crustaceans', 'id' => '3'),
                                                                                             array('value' => 'Egg', 'id' => '4'),
                                                                                             array('value' => 'Penuts', 'id' => '5'),
                                                                                             array('value' => 'Soybeans', 'id' => '6'),
                                                                                             array('value' => 'Dairy', 'id' => '7'),
                                                                                             array('value' => 'Celery', 'id' => '8'),
                                                                                             array('value' => 'Mustard', 'id' => '9'),
                                                                                             array('value' => 'Sesame Seeds', 'id' => '10'),
                                                                                             array('value' => 'Sulpher Dioxide', 'id' => '11'),
                                                                                             array('value' => 'Lupin', 'id' => '12'),
                                                                                             array('value' => 'Molluscs', 'id' => '13'))),
                         array('id'=>'1', 'optionsCategory' => 'Food Group', 'options' => array(array('value' => 'Fish', 'id' => '14'),
                                                                                                array('value' => 'Vegetable', 'id' => '15'),
                                                                                                array('value' => 'Other', 'id' => '16'),
                                                                                                array('value' => 'Meat', 'id' => '17'),
                                                                                                array('value' => 'Confectionary', 'id' => '18'))));

        $template = $twig->loadTemplate("search/search.twig");
        return $template->render(array('config' => $config,
                                       'filters' => $filters
                                      ));
    }
}