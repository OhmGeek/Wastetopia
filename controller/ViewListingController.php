<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 01/03/17
 * Time: 21:30
 */

namespace Wastetopia\Controller;


use Wastetopia\Model\Listing;

class ViewListingController
{
    public function __construct()
    {

    }

    public function getListingPage($listingID) {
        // query the model for the item data.
        $model = new Listing();
        $details = $model->getDetails($listingID);
        // process it
        return json_encode($details, true);
        // output it on the screen
    }
}