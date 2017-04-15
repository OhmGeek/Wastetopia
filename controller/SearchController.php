<?php

namespace Wastetopia\Controller;

use Wastetopia\Model\SearchModel;
use Wastetopia\Model\CardDetailsModel;
use Wastetopia\Model\UserCookieReader;

class SearchController
{
    public function __construct()
    {
        $this->searchModel = new SearchModel();
        $this->cardDetailsModel = new CardDetailsModel();
    }

    public function recommendationSearch($tagsArr, $currentUserID)
    {
        $results = $this->searchModel->getReccomendationResults($tagsArr, $currentUserID);
        $ids = array_slice($results, 0, 4);

        $searchResults = [];
        foreach ($ids as $item) {
            $result = $this->searchModel->getCardDetails($item["ListingID"]);
            $result2 = $this->searchModel->getDefaultImage($item["ListingID"]);
            $searchResults[] = array_merge($result, $result2);
        }

        return $searchResults;
    }


    //TODO add notTags and distance limit to search fucntion
    public function JSONSearch($lat, $long, $search, $tagsArr, $notTagsArr, $distanceLimit, $pageNumber, $order)
    {
        $reader = new UserCookieReader();
        $userID = $reader->get_user_id();

        $offset = 30*intval($pageNumber);
        $limit = $offset + 30;
        $itemInformation = $this->search($lat, $long, $search, $tagsArr);

        var_dump($order);
        switch ($order) {
            case 'D':
                if (($lat !== "") && ($long !== ""))
                {
                    $sortedInformation = $this->distanceSort($itemInformation, $lat, $long);
                }
                else
                {
                    $sortedInformation = $itemInformation;
                }
                break;

            case 'AZ':
                $sortedInformation = $this->alphabetSort($itemInformation);
                break;
            
            case 'ZA':
                $sortedInformation = $this->reverseAlphabetSort($itemInformation);
                break;

            case 'UR':
                $sortedInformation = $this->userPopularitySort($itemInformation);
                break;
            
            default:
                $sortedInformation = $itemInformation;
                break;
        }
        
        


        $searchResults = [];
        foreach ($sortedInformation as $item)
        {
            $result = $this->searchModel->getCardDetails($item["ListingID"]);
            $result2 = $this->searchModel->getDefaultImage(17);
            $result['isRequesting'] = $this->searchModel->isRequesting($item["ListingID"], $userID);
            $result['isWatching'] = $this->searchModel->isWatching($item["ListingID"], $userID);
            $searchResults[] = array_merge($result, $result2);
        }

        $pageResults = array_slice($searchResults, $offset, $limit);
        return json_encode($pageResults);
    }

    public function MAPSearch($lat, $long, $search, $tagsArr, $notTagsArr, $distanceLimit)
    {
        $itemInformation = $this->search($lat, $long, $search, $tagsArr);

        $searchResults = [];
        foreach ($itemInformation as $item)
        {
            $result = $this->searchModel->getCardDetails($item["ListingID"]);
            $result2 = $this->searchModel->getDefaultImage(17);
            $searchResults[] = array_merge($result, $result2);
        }

        return json_encode($searchResults);
    }

    /*lat = Latitude
      long = Longitude 
      $search = Search term
      $tagsArr = array of item tags */

    /*Limit and Offset are implemented in the wrapper functions
      As custom sorting is needed in the search controller it cannot be done in SQL*/

    private function search($lat, $long, $search, $tagsArr)
    {
        $distanceSearch  = false;
        $nameSearch = false;
        $tagSearch = false;

        if(!empty($lat) && !empty($long))
        {
            $distanceSearch = true;
        }
        if (!empty($search))
        {
            $nameSearch = true;
        }
        if(!empty($tagsArr[0]))
        {
            $tagSearch = true;
        }

        if ($distanceSearch && $nameSearch && $tagSearch) {
            $itemInformation = $this->searchModel->getSearchResults($lat, $long, $search, $tagsArr);  //Distance, Name and Tags
        }
        elseif ($distanceSearch && $nameSearch && !$tagSearch) {
            $itemInformation = $this->searchModel->getSearchResults($lat, $long, $search, false);      //Distance and Name
        }
        elseif ($distanceSearch && !$nameSearch && $tagSearch) {
            $itemInformation = $this->searchModel->getSearchResults($lat, $long, false, $tagsArr);     //Distance and Tags
        }
        elseif (!$distanceSearch && $nameSearch && $tagSearch) {
            $itemInformation = $this->searchModel->getSearchResults(false, false, $search, $tagsArr);   //Name and Tags
        }
        elseif ($distanceSearch && !$nameSearch && !$tagSearch) {
            $itemInformation = $this->searchModel->getSearchResults($lat, $long, false, false);         //Distance only
        }
        elseif (!$distanceSearch && $nameSearch && !$tagSearch) {
            $itemInformation = $this->searchModel->getSearchResults(false, false, $search, false);       //Name only
        }
        elseif (!$distanceSearch && !$nameSearch && $tagSearch) {
            $itemInformation = $this->searchModel->getSearchResults(false, false, false, $tagsArr);      //Tags only
        }
        else {
            $itemInformation = $this->searchModel->getSearchResults(false, false, false, false);          //No filtering
        }
               
        return $itemInformation;
    }

    function distanceSort($itemList, $latitude, $longitude){

        $userLocation = array('lat' => $latitude, 'long' => $longitude);

        foreach ($itemList as $key => $item)
        {
            $itemLocation = array('lat' => $item['Latitude'], 'long' => $item['Longitude']);
            $distance = $this->haversineDistance($userLocation, $itemLocation);
            $itemList[$key]['distance'] = $distance;           
        }

        usort($itemList, function($a, $b)
        {
            if ($a['distance'] < $b['distance']) {return 1;}
            elseif ($a['distance'] > $b['distance']) {return -1;}
            else {return 0;}
        });

        return $itemList;
    }
    function alphabetSort($itemList){

        usort($itemList, function($a, $b)
        {
            return strcasecmp($a['Name'], $b['Name']);
        });

        return $itemList;
    }
    function reverseAlphabetSort($itemList){

        usort($itemList, function($a, $b)
        {
            $bool = strcasecmp($a['Name'], $b['Name']);

            if($bool < 0) {return 1;}
            elseif($bool > 0) {return -1;}
            else{return 0;} 
        });

        return $itemList;
    }
    function userPopularitySort($itemList){
        return $itemList;
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