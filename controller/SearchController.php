<?php

namespace Wastetopia\Controller;

use Wastetopia\Model\SearchModel;

class SearchController
{
    public function __construct()
    {
        $this->searchModel = new SearchModel();
    }


    public function test()
    {
        $searchModel = new SearchModel();
        $listingIDs = $searchModel->getNearbyItems(0, 0.7);

        return $this->haversineDistance(array('lat' => 0, 'long' => 0), array('lat' => 5.804925, 'long' => 0.728986));


        var_dump($listingIDs);
    }
    public function basicSearch($searchTerm)
    {
        $searchModel = new SearchModel();
        $listingIDs = $searchModel->getListingIDsFromName($searchTerm);

        $searchResultsForJSON = [];

        foreach ($listingIDs as $item)
        {
            $listingResults = $this->searchModel->getCardDetails(intval($item['ListingID']));
            array_push($searchResultsForJSON, $listingResults[0]);
        }

        return json_encode($searchResultsForJSON);
    }

    public function distanceSearch($lat, $long, $search, $tags)
    {
        $userLocation = array('lat' => $lat,'long' => $long);
        $tagsArray = explode('+', $tags);

        $itemInformation = $this->searchModel->getNearbyItems($lat, $long, $search, $tagsArray); //Use default distance cap
        foreach ($itemInformation as $key => $item)
        {
            $itemLocation = array('lat' => $item['Latitude'], 'long' => $item['Longitude']);
            $distance = $this->haversineDistance($userLocation, $itemLocation);
            $itemInformation[$key]['distance'] = $distance;           
        }

        usort($itemInformation, function($a, $b)
        {
            if ($a['distance'] < $b['distance']) {return 1;}
            elseif ($a['distance'] > $b['distance']) {return -1;}
            else {return 0;}
        });

        $searchResults = [];
        foreach ($itemInformation as $item) {
            $result = $this->searchModel->getCardDetails($item["ListingID"]);
            $searchResults[] = $result;
        }
        return json_encode($searchResults);
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
        $radLat1 = $latLong1['lat'] * (M_PI/180);
        $radLong1 = $latLong1['long'] * (M_PI/180);
        $radLat2 = $latLong2['lat'] * (M_PI/180);
        $radLong2 = $latLong2['long'] * (M_PI/180);

        $radius = floatval('6371e3');  //Radius of the earth in meters
        
        $haversineDiffLat = $this->haversine($radLat1 - $radLat2);
        $haversineDiffLong = $this->haversine($radLong1 - $radLong2);
        $haversineLongCosLat = cos($radLat1) * cos($radLong2) * $haversineDiffLong;
        $distance = 2*$radius*asin(sqrt($haversineDiffLat+$haversineLongCosLat));

        return $distance; 
    
    }
    function haversine($theta)
    {
        return (sin($theta/2)) ** 2;
    }

}