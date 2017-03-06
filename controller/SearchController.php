<?php

Class SearchController
{
    function __construct($searchTerm)
    {
        $this->generatePage($searchTerm);
    }
    function generatePage($searchTerm)
    {
        $model = new SearchModel();

        $itemList = array();
        $itemIDList = $model->getItemIDsFromSearch($searchTerm);
        foreach ($itemIDList as $itemID)
        {
            //Odd syntax for pushing an item onto an array, much faster than array_push()
            $itemDataList[] = $model->getItemDataFromID((int)$itemID['listing_id']);
        }

        $loader = new Twig_Loader_Filesystem('../../view/');
        $twig = new Twig_Environment($loader);

        $itemList = array();
        $template = $twig->loadTemplate("item.twig");
        foreach ($itemDataList as $item)
        {
            if (isset($item[0]['default_user_image_url']))
            {
                $itemImagePath = $item[0]['default_user_image_url'];
            }
            else
            {
                $itemImagePath = $item[0]["default_item_image_url"];
            }
            $itemName = $item[0]["item_name"];
            $itemSummary = $item[0]["user_description"];
            $userImage = $item[0]["profile_picture_url"];
            $userName = $item[0]['forename']." ".$item[0]['surname'];
            $dateTime = new DateTime($item[0]["date_added"]);
            $dateAdded = $dateTime->format("Y-m-d");




            $output = $template->render(array('itemImagePath' => $itemImagePath,
                                              'itemName'      => $itemName,
                                              'itemSummary'   => $itemSummary,
                                              'userImage'     => $userImage,
                                              'username'      => $userName,
                                              'dateAdded'     => $dateAdded));
            $itemList[] = $output;
        }
        $template = $twig->loadTemplate("search.twig");
        print($template->render(array('itemList' => $itemList)));
    }
}