<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 01/03/17
 * Time: 21:30
 */

namespace Wastetopia\Controller;


use Wastetopia\Model\ViewItemModel;

class ViewItemController
{
    public function __construct()
    {

    }

    /**
     * @param $listingID
     * @return string
     */
    public function getListingPage($listingID) {
        // query the model for the item data.
        $model = new ViewItemModel();
        $details = $model->getAllInOneQuery($listingID);
        // process it
        return json_encode($details, true);
        // output it on the screen
    }
}