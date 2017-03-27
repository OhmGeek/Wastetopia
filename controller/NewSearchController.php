<?php

namespace Wastetopia\Controller;

use Wastopia\Model\SearchModel;

class SearchController
{
    public function __construct()
    {
        /*decide search type*/
    }

    public function basicSearch()
    {
        $searchTerm = $_GET['searchTerm'];

        $searchModel = new SearchModel();
        $listingIDs = $searchModel->getListingIDsFromName($searchTerm);

        $searchResultsForJSON = [];

        foreach ($listingIDs as $ID)
        {
            $listingResults = $searchModel($ID);
            $searchResultsForJSON[] = $listingResults;
        }

        return json_encode($searchResultsForJSON);

    }

}