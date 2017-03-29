<?php

namespace Wastetopia\Controller;

use Wastetopia\Model\SearchModel;

class SearchController
{
    public function __construct()
    {
        /*decide search type*/
    }

    public function basicSearch($searchTerm)
    {
        $searchModel = new SearchModel();
        $listingIDs = $searchModel->getListingIDsFromName($searchTerm);

        $searchResultsForJSON = ["test","test",$listingIDs, "like what?"];

        foreach ($listingIDs as $ID)
        {
            $listingResults = $searchModel->getCardDetails($ID);
            $searchResultsForJSON[] = $listingResults;
        }

        return json_encode($searchResultsForJSON);

    }

    public function sampleSearch()
    {
        $var = '[{
                    "lat": 54.767289,
                    "long": -1.570361,
                    "img": "flowers.jpg",
                    "username": "Chen Hemsworth",
                    "user_id": 101,
                    "date_added": "29/03/17",
                    "item_name": "Pina Collada",
                    "item_id": 301
                  },
                  {
                    "lat": 54.767672,
                    "long": -1.570551,
                    "img": "fruit.jpg",
                    "username": "Bryan Collins",
                    "user_id": 102,
                    "date_added": "29/03/17",
                    "item_name": "Strawberry Daquri",
                    "item_id": 302
                  },
                  {
                    "lat": 54.767441,
                    "long": -1.57204,
                    "img": "veg.jpg",
                    "username": "Stephan Church",
                    "user_id": 103,
                    "date_added": "29/03/17",
                    "item_name": "Mojito",
                    "item_id": 303
                  },
                  {
                    "lat": 54.767441,
                    "long": -1.57204,
                    "img": "donut.jpg",
                    "username": "Stephan Church",
                    "user_id": 103,
                    "date_added": "29/03/17",
                    "item_name": "Margharita",
                    "item_id": 304
                  }]';
        return $var;
    }

}