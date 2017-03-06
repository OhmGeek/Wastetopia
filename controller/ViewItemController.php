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

        // output it on the screen
        // query the model for the item data.
        $model = new ViewItemModel();
        $details = $model->getAll($listingID);
        // process it
        //todo return twig
        return json_encode($details, true);
    }

    public function getListingDetailsAsJSON($listingID) {
        // query the model for the item data.
        $model = new ViewItemModel();
        $details = $model->getAll($listingID);
        // process it
        return json_encode($details, true);
    }
}