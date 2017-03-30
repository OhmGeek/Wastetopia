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

        $searchResultsForJSON = [];

        foreach ($listingIDs as $item)
        {
            $listingResults = $searchModel->getCardDetails(intval($item['ListingID']));
            array_push($searchResultsForJSON, $listingResults[0]);
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

    /*Calculate the distance between point 1 and 2 using the Haversine formula*/
    function haversineDistance($latLong1, $latLong2)
    {
        $RadLat1 = $latLong1['lat'] * (M_PI/180);
        $RadLong1 = $latLong1['long'] * (M_PI/180);
        $RadLat2 = $latLong2['lat'] * (M_PI/180);
        $RadLong2 = $latLong2['long'] * (M_PI/180);

        $radius = floatval('6371e3');  //Radius of the earth in meters
        
        $haversineDiffLat = $this->haversine($RadLat1 - $RadLat2);
        $haversineDiffLong = $this->haversine($RadLong1 - $RadLong2);
        $haversineLongCosLat = cos($RadLat1) * cos($RadLong2) * $haversineDiffLong;
        $distance = 2*$radius*asin(sqrt($haversineDiffLat+$haversineLongCosLat));

        return $distance; 
    
    }
    function haversine($theta)
    {
        return (sin($theta/2)) ** 2;
    }

}