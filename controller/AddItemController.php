<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 06/03/17
 * Time: 16:15
 */

namespace Wastetopia\Controller;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Wastetopia\Config\CurrentConfig;
use Wastetopia\Model\AddItemModel;
use Wastetopia\Model\AmazonS3;
use Wastetopia\Model\HeaderInfo;

class AddItemController
{
    public function __construct()
    {
        $this->model = new AddItemModel();
    }

    /**
     * @return string (HTML for the add item page)
     */
    public function renderAddPage() {

        $loader  = new Twig_Loader_Filesystem(__DIR__.'/../view/');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('items/edit_items.twig');
        return $template->render(array(
            'tags' => $this->getListOfTagsForView(),
            "config" => CurrentConfig::getAll(),
            "header" => HeaderInfo::get()
        )); // todo add required details here.
    }


    /**
     * @return array (object containing list of tag names to display on the form)
     */
    public function getListOfTagsForView() {
        return array(
            'type' => $this->model->getAllTagOptions(1),
            'classification' => $this->model->getAllTagOptions(5),
            'dietary' => $this->model->getAllTagOptions(3),
            'contains' => $this->model->getAllTagOptions(4),
            'state' => $this->model->getAllTagOptions(2)
        );
    }

    public function getListOfFiltersForView() {
        return array(
            '1' => $this->model->getAllTagOptionsFilter(1),
            '5' => $this->model->getAllTagOptionsFilter(5),
            '3' => $this->model->getAllTagOptionsFilter(3),
            '4' => $this->model->getAllTagOptionsFilter(4),
            '2' => $this->model->getAllTagOptionsFilter(2)
        );
    }
    // this code below flattens the selected tags into one list
    /**
     * @param $details (the item object as serialized by JavaScript)
     * @return array (list of tags)
     */
    private function generateTags($details) {
        $properties = array('classification', 'dietary', 'contains', 'state');
        // create a tag collection, collating all tags
        $listOfTags = array();
        // go through all properties individually, getting the tag details.
        foreach($properties as $prop) {
            // now go through the dietary requirements
            // only do this if the array itself is defined (as errors will be thrown otherwise
            if (is_array($details[$prop]) || is_object($details[$prop])) {
                foreach ($details[$prop] as $t) {
                    $dietTag = $this->model->getTagDetails($t);
                    if (isset($dietTag) && $dietTag['name'] != null) {
                        array_push($listOfTags, $dietTag);
                    }
                }
            }
        }
        return $listOfTags;
    }

    /**
     * @param $details (the item object as serialized by JavaScript)
     * @return array (list of images)
     */
    private function getImageArray($details) {
        // we are given a list of urls
        // we need just to add the filetype to the array
        $imageArray = array();
        error_log("Get image array from user, then all the urls");
        error_log(json_encode($details['image']));
        foreach($details['images'] as $img) {
            $obj = array(
                'fileType' => 'img',
                'url' => $img,
                'isDefault' => 0
            );
            error_log($img);
            array_push($imageArray, $obj);
        }
        return $imageArray;
    }

    /**
     * Add an item to the DB
     * @param $details (a serialized item)
     */
    public function addItem($details) {
        $info = array(
            'item' => array(
                'itemName' => $details['name'],
                'itemDescription' => $details['description'],
                'useByDate' => $details['expires'],
                'quantity' => $details['quantity']
            ),
            'images' => $this->getImageArray($details),
            'tags' => $this->generateTags($details),
            'location' => $details['location'],
            'barcode' => $details['barcode']
            );

        error_log('Now for the Serialized Item after changes:');
        error_log(json_encode($info));
        $listingID = $this->model->mainAddItemFunction(
            $info['item'],
            $info['tags'],
            $info['images'],
            $info['barcode'],
            $info['location']
        );
        return array("listingID" => $listingID);
    }

    /**
     * Add an item image to S3 and the DB
     * @param $files (array of files to upload)
     * @return string (JSON containing image url data)
     */
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
            error_log("This is addItemImage: url, then files");
            error_log($id);
            error_log($url);
            array_push($uploadedImages,$image);
        }
        error_log("Test");
        error_log(json_encode($uploadedImages));
        return json_encode($uploadedImages); //encode the image output as json.
    }

}