<?php

namespace Wastetopia\Controller;

use Wastetopia\Controller\Authenticator;
use Wastetopia\Model\SearchModel;
use Wastetopia\Model\CardDetailsModel;
use Wastetopia\Model\UserCookieReader;
use Wastetopia\Config\CurrentConfig;

class SearchController
{
    public function __construct()
    {
        $this->searchModel = new SearchModel();
        $this->cardDetailsModel = new CardDetailsModel();
    }

    public function recommendationSearch($tagsArr, $currentUserID = null)
    {
        $results = $this->searchModel->getReccomendationResults($tagsArr, $currentUserID);
        $ids = array_slice($results, 0, 4);

        $searchResults = [];
        foreach ($ids as $item)
        {
            $result = $this->searchModel->getCardDetails($item["ListingID"]);
            $result['isRequesting'] = $this->searchModel->isRequesting($item["ListingID"], $userID);
            $result['isWatching'] = $this->searchModel->isWatching($item["ListingID"], $userID);

            $image = $this->searchModel->getDefaultImage($item["ListingID"]);
            if(empty($image))
            {
                $config = new CurrentConfig();
                $result['Image_URL'] = $config->getProperty('ROOT_IMG') . '/PCI.png';
            }
            else
            {
                $result['Image_URL'] = $image['Image_URL'];
            }
            $searchResults[] = $result;
        }

        return $searchResults;
    }


    //TODO add notTags and distance limit to search fucntion
    public function JSONSearch($lat, $long, $search, $tagsArr, $notTagsArr, $distanceLimit, $pageNumber, $order, $quantity)
    {
        $reader = new UserCookieReader();
        $userID = $reader->get_user_id();

        $offset = 30*intval($pageNumber);
        $limit = $offset + 30;

        $distance = $distanceLimit * 1000.0; /*Convert Km in m*/

        $itemInformation = $this->search($lat, $long, $search, $tagsArr, $notTagsArr, $quantity);

        /*Remove items based on distance limit*/
        $newItemInformation = array();
        $userLoc = array('lat' => $lat, 'long' => $long);
        foreach ($itemInformation as $key => $item) {
            $itemLoc = array('lat' => $item['Latitude'], 'long' => $item['Longitude']);
            if($this->haversineDistance($userLoc, $itemLoc) < $distance)
            {
                $newItemInformation[] = $item;
            }
        }
        $itemInformation = $newItemInformation;

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

            case 'DT':
                $sortedInformation = $this->newFirstSort($itemInformation);
                var_dump("expression");
                break;

            default:
                $sortedInformation = $itemInformation;
                break;
        }




        $searchResults = [];
        foreach ($sortedInformation as $item)
        {
            $result = $this->searchModel->getCardDetails($item["ListingID"]);
            $result['isRequesting'] = $this->searchModel->isRequesting($item["ListingID"], $userID);
            $result['isWatching'] = $this->searchModel->isWatching($item["ListingID"], $userID);
            $result['isLoggedIn'] = Authenticator::isAuthenticated();

            $image = $this->searchModel->getDefaultImage($item["ListingID"]);
            if(empty($image))
            {
                $config = new CurrentConfig();
                $result['Image_URL'] = $config->getProperty('ROOT_IMG') . '/PCI.png';
            }
            else
            {
                $result['Image_URL'] = $image['Image_URL'];
            }
            $searchResults[] = $result;
        }

        $pageResults = array_slice($searchResults, $offset, $limit);
        return json_encode($pageResults);
    }

    public function MAPSearch($lat, $long, $search, $tagsArr, $notTagsArr, $distanceLimit, $quantity)
    {
        $itemInformation = $this->search($lat, $long, $search, $tagsArr, $notTagsArr, $quantity);

        $searchResults = [];
        foreach ($itemInformation as $item)
        {
            $result = $this->searchModel->getCardDetails($item["ListingID"]);
            $result['isRequesting'] = $this->searchModel->isRequesting($item["ListingID"], $userID);
            $result['isWatching'] = $this->searchModel->isWatching($item["ListingID"], $userID);
            $result['isLoggedIn'] = Authenticator::isAuthenticated();

            $image = $this->searchModel->getDefaultImage($item["ListingID"]);
            if(empty($image))
            {
                $config = new CurrentConfig();
                $result['Image_URL'] = $config->getProperty('ROOT_IMG') . '/PCI.png';
            }
            else
            {
                $result['Image_URL'] = $image['Image_URL'];
            }
            $searchResults[] = $result;
        }

        return json_encode($searchResults);
    }

    /*lat = Latitude
      long = Longitude
      $search = Search term
      $tagsArr = array of item tags */

    /*Limit and Offset are implemented in the wrapper functions
      As custom sorting is needed in the search controller it cannot be done in SQL*/

    private function search($lat, $long, $search, $tagsArr, $notTagsArr, $quantity)
    {

        if(empty($lat) || empty($long))
        {
            $lat = false;
            $long = false;
        }
        if (empty($search))
        {
            $search = false;
        }
        if(empty($tagsArr[0]))
        {
            $tagsArr = false;
        }
        if(empty($notTagsArr[0]))
        {
            $notTagsArr = false;
        }


        $itemInformation = $this->searchModel->getSearchResults($lat, $long, $search, $tagsArr, $notTagsArr, $quantity);

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
    function newFirstSort($itemList){
        usort($itemList, function($a, $b)
        {
            $aDT = new DateTime($a['Time_Of_Creation']);
            $bDT = new DateTime($b['Time_Of_Creation']);

            if($aDT < $bDT) {return 1;}
            elseif($aDT > $bDT) {return -1;}
            else{return 0;}
        });

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
