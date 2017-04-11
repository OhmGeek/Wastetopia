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
use Wastetopia\Model\AmazonS3;

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

    private function generateTags($details) {
        $properties = array('classification', 'dietary', 'contains');
        // create a tag collection, collating all tags
        $listOfTags = array();
        // go through all properties individually, getting the tag details.
        foreach($properties as $prop) {
            // now go through the dietary requirements
            foreach ($details[$prop] as $t) {
                $dietTag = $this->model->getTagDetails($t);
                if (isset($dietTag)) {
                    array_push($listOfTags, $dietTag);
                }
            }
        }
        return $listOfTags;
    }

    private function getImageArray($details) {
        // we are given a list of urls
        // we need just to add the filetype to the array
        $imageArray = array();
        foreach($details['images'] as $img) {
            $obj = array(
                'fileType' => 'img',
                'url' => $img,
                'isDefault' => 0
            );
            array_push($imageArray, $obj);
        }
        return $imageArray;
    }

    public function addItem($details) {
        $info = array(
            'item' => array(
                'itemName' => $details['name'],
                'itemDescription' => $details['description'],
                'useByDate' => $details['expires'],
                'quantity' => 1
            ),
            'images' => $this->getImageArray($details),
            'tags' => $this->generateTags($details),
            'location' => $details['location']
            );

        $this->model->mainAddItemFunction(
            $info['item'],
            $info['tags'],
            $info['images'],
            $info['barcode'],
            $info['location']
        );
    }

    public function addItemImage($files) {
        // this adds an item to S3 and to the DB, returning the Id and url
        error_log(json_encode($files));
        $s3 = new AmazonS3();
        $urls = $s3->upload($files);
        $uploadedImages = array();
        error_log(json_encode($urls));
        foreach($urls as $url) {
            $id = $this->model->addToImageTable('img', $url);
            // now let's create an object inside
            $image = array(
                "id" => $id,
                "url" => $url
            );
            error_log($id);
            error_log($url);
            array_push($uploadedImages,$image);
        }
        error_log("Test");
        error_log(json_encode($uploadedImages));
        return json_encode($uploadedImages); //encode the image output as json.
    }

}