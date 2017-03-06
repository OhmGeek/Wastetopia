<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 06/03/17
 * Time: 16:15
 */

namespace Wastetopia\Controller;

use Twig_Loader_Filesystem;
use Twig_Environment;
use Wastetopia\Model\AddItemModel;

class AddItemController
{
    public function __construct()
    {
        $loader  = new Twig_Loader_Filesystem(__DIR__.'/../view/');
        $this->twig = new Twig_Environment($loader);
        $this->model = new AddItemModel();
    }

    public function renderAddPage() {

        # this renders the page :D
        $template = $this->twig->loadTemplate('add_item.html');
        return $template->render(array()); // this is the data
    }

    public function addItem($details) {
        $this->model->mainAddItemFunction($details['items'], $details['tags'],
            $details['images'], $details['barcode'],$details['location']);

    }

}