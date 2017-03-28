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

        foreach ($listingIDs as $ID)
        {
            $listingResults = $searchModel->getCardDetails($ID);
            $searchResultsForJSON[] = $listingResults;
        }

        return json_encode($searchResultsForJSON);

    }

}