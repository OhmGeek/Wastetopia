<?php

namespace Wastetopia\Controller;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Wastetopia\Config\CurrentConfig;

use Wastetopia\Model\HeaderInfo;

class SearchPageController
{
    function __construct()
    {
    }

    function render($getVars)
    {
        $loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

        $searchTerm = $getVars[0];
        $postcode = $getVars[1];
        $lat = $getVars[2];
        $long = $getVars[3];
        
        $escapedSearch = array();
        $escapedSearch['search'] = htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');
        $escapedSearch['postcode'] = htmlspecialchars($postcode, ENT_QUOTES, 'UTF-8');
        $escapedSearch['lat'] = htmlspecialchars((string)$lat, ENT_QUOTES, 'UTF-8');
        $escapedSearch['long'] = htmlspecialchars((string)$long, ENT_QUOTES, 'UTF-8');
        $escapedSearch['advancedSearch'] = false;


        $currentConfig = new CurrentConfig();
        $config = $currentConfig->getAll();

        $filters = $this->getSearchFilters();        

        $template = $twig->loadTemplate("search/search.twig");
        return $template->render(array('config' => $config,
                                       "header" => HeaderInfo::get(),
                                       'filters' => $filters,
                                       'searchTerm' => json_encode($escapedSearch)
                                      ));
    }
    function renderAdvanced($postVars)
    {
        $loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

        $searchTerm = $postVars->search;
        $postcode = $postVars->postcode;
        $lat = $postVars->lat;
        $long = $postVars->lng;
        $quantity = $postVars->quantity;
        $distance = $postVars->distance;
        $filters = $postVars->filters;
        $sort = $postVars->sort;

        
        $escapedSearch = array();
        $escapedSearch['search'] = htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');
        $escapedSearch['postcode'] = htmlspecialchars($postcode, ENT_QUOTES, 'UTF-8');
        $escapedSearch['lat'] = htmlspecialchars((string)$lat, ENT_QUOTES, 'UTF-8');
        $escapedSearch['long'] = htmlspecialchars((string)$long, ENT_QUOTES, 'UTF-8');
        $escapedSearch['quantity'] = htmlspecialchars((string)$quantity, ENT_QUOTES, 'UTF-8');
        $escapedSearch['distance'] = htmlspecialchars((string)$distance, ENT_QUOTES, 'UTF-8');
        $escapedSearch['filters'] = htmlspecialchars($filters, ENT_QUOTES, 'UTF-8');
        $escapedSearch['sortOption'] = htmlspecialchars($sort, ENT_QUOTES, 'UTF-8');
        $escapedSearch['advancedSearch'] = true;


        $currentConfig = new CurrentConfig();
        $config = $currentConfig->getAll();

        $filters = $this->getSearchFilters();        

        $template = $twig->loadTemplate("search/search.twig");
        return $template->render(array('config' => $config,
                                       'filters' => $filters,
                                       'searchTerm' => json_encode($escapedSearch)
                                      ));
    }

    function getSearchFilters()
    {
        $filters = array(array('id'=>'0', 'optionsCategory'=>'Allergens', 'type' => 'negative',
                               'options' => array(array('value' => 'Nut Free', 'id' => '1'),
                                                  array('value' => 'Gluten Free', 'id' => '2'),
                                                  array('value' => 'Crustacean Free', 'id' => '3'),
                                                  array('value' => 'Egg Free', 'id' => '4'),
                                                  array('value' => 'Penut Free', 'id' => '5'),
                                                  array('value' => 'Soybean Free', 'id' => '6'),
                                                  array('value' => 'Milk Free', 'id' => '7'),
                                                  array('value' => 'Celery Free', 'id' => '8'),
                                                  array('value' => 'Mustard Free', 'id' => '9'),
                                                  array('value' => 'Sesame Seeds Free', 'id' => '10'),
                                                  array('value' => 'Sulpher Dioxide Free', 'id' => '11'),
                                                  array('value' => 'Lupin Free', 'id' => '12'),
                                                  array('value' => 'Mollusc Free', 'id' => '13'))),
                         array('id'=>'1', 'optionsCategory' => 'Food Group', 'type' => 'positive',
                               'options' => array(array('value' => 'Fish', 'id' => '14'),
                                                  array('value' => 'Vegetable', 'id' => '15'),
                                                  array('value' => 'Other', 'id' => '16'),
                                                  array('value' => 'Meat', 'id' => '17'),
                                                  array('value' => 'Confectionary', 'id' => '18'),
                                                  array('value' => 'Bread', 'id' => '23'),
                                                  array('value' => 'Alcohol', 'id' => '34'),
         array('value' => 'Dairy', 'id' => '37'))),
                         array('id'=>'2', 'optionsCategory' => 'Other', 'type' => 'positive',
                               'options' => array(array('value' => 'Chilled', 'id' => '21'),
                                                  array('value' => 'Frozen', 'id' => '22'),
                                                  array('value' => 'Unopened', 'id'=>'28'),
                                                  array('value' => 'Damaged', 'id'=>'29'),
                                                  array('value' => 'No use by date', 'id'=>'30'),
                                                  array('value' => 'Large Item', 'id'=>'31'))),
                         array('id'=>'3', 'optionsCategory' => 'Dietary Requirements', 'type' => 'positive',

                               'options' => array(array('value'=>'Kosher', 'id'=>'24'),
                                                  array('value'=>'Halal', 'id'=>'25'),
                                                  array('value'=>'Vegetarian', 'id'=>'26'),
                                                  array('value'=>'Vegan', 'id'=>'27'))));
        return $filters;
    }
}
