<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 01/03/17
 * Time: 21:30
 */

namespace Wastetopia\Controller;


use Twig_Environment;
use Twig_Loader_Filesystem;
use Wastetopia\Config\CurrentConfig;
use Wastetopia\Model\HeaderInfo;
use Wastetopia\Model\ListingModel;
use Wastetopia\Model\UserCookieReader;
use Wastetopia\Model\ViewItemModel;

class ViewItemController
{

    public function __construct()
    {
        // query the model for the item data.
        $this->model = new ViewItemModel();
    }

    /**
     * Get the listing page for an item
     * @param $listingID (the listingID to display)
     * @return string (the HTML twig response)
     */
    public function getListingPage($listingID) {
        // get the data
        $details = $this->model->getAll($listingID);

        //using twig, start to render the listing page on the screen
        $loader  = new Twig_Loader_Filesystem(__DIR__.'/../view/');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('items/view_item.twig');

        // add in the header info (whether the user is logged in or not)
        $details = array_merge($details, array("header" => HeaderInfo::get()))
            $details = array_merge($details, array("config" => CurrentConfig::getAll()));
        return $template->render($details);
    }

    /**
     * Get JSON of the current listing details
     * @param $listingID (listing ID of the item to display)
     * @return string (the JSON data)
     */
    public function getListingDetailsAsJSON($listingID) {

        $details = $this->model->getAll($listingID);
        // process it
        return json_encode($details);
    }
}