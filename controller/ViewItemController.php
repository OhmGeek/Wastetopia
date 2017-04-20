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

        $loader  = new Twig_Loader_Filesystem(__DIR__.'/../view/');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('items/view_item.twig');

        return $template->render($details);
    }

    public function getListingDetailsAsJSON($listingID) {
        // query the model for the item data.
        $model = new ViewItemModel();
        $details = $model->getAll($listingID);
        // process it
        return json_encode($details);
    }
}