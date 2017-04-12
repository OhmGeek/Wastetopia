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
                                                                                                array('value' => 'Confectionary', 'id' => '18'),
                                                                                                array('value' => 'Bread', 'id' => '23'))),
                         array('id'=>'2', 'optionsCategory' => 'Other', 'options' => array(array('value' => 'Chilled', 'id' => '21'),
                                                                                           array('value' => 'Frozen', 'id' => '22'),
                                                                                           array('value' => 'Unopened', 'id'=>'28'),
                                                                                           array('value' => 'Damaged', 'id'=>'29'),
                                                                                           array('value' => 'No use by date', 'id'=>'30'),
                                                                                           array('value' => 'Large Item', 'id'=>'31'))),
                         array('id'=>'3', 'optionsCategory' => 'Dietery Requirements', 'options' => array(array('value'=>'Kosher', 'id'=>'24'),
                                                                                                          array('value'=>'Halal', 'id'=>'25'),
                                                                                                          array('value'=>'Vegetarian', 'id'=>'26'),
                                                                                                          array('value'=>'Vegan', 'id'=>'27'))));

        $template = $twig->loadTemplate("search/search.twig");
        return $template->render(array('config' => $config,
                                       'filters' => $filters
                                      ));
    }
}