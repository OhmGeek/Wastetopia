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
        $this->model = new AddItemModel();
    }

    public function renderAddPage() {

        $loader  = new Twig_Loader_Filesystem(__DIR__.'/../view/');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('items/edit_items.twig');
        return $template->render(array()); // todo add required details here.
    }

    public function addItem($details) {
        $this->model->mainAddItemFunction($details['items'], $details['tags'],
            $details['images'], $details['barcode'],$details['location']);

    }

}