<?php
/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 14/04/2017
 * Time: 00:51
 */

namespace Wastetopia\Controller;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Wastetopia\Model\AnalysisModel;
use Wastetopia\Config\CurrentConfig;


class AnalysisController
{

    /**
     * AnalysisController constructor.
     */
    public function __construct()
    {
        // Create instance of AnalysisModel
        $this->model = new AnalysisModel();

        //Create twig loader
        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);
    }


    /**
     * Generates the HTML for the analysis page
     * @return mixed
     */
    function generatePage(){
        // Get sending frequencies
        $sendingFrequencies = json_decode($this->getTotalNameFrequenciesSending());

        $sendingNames = array();

        foreach($sendingFrequencies as $key=>$value){
            $details = array();
            $details["name"] = $key;
            $details["frequency"] = $value;

            array_push($sendingNames, $details);
        }

        // Same for receiving
        $receivingFrequencies = json_decode($this->getTotalNameFrequenciesReceiving());

        $receivingNames = array();

        foreach($receivingFrequencies as $key=>$value){
            $details = array();
            $details["name"] = $key;
            $details["frequency"] = $value;

            array_push($receivingNames, $details);
        }

        // Get config
        $CurrentConfig = new CurrentConfig();
        $config = $CurrentConfig->getAll();

        //Create array for Twig file
        $output = array(
            "config" => $config,
            "sendingNames"=>$sendingNames,
            "receivingNames"=>$receivingNames
        );

        //Load template and print result
        $template = $this->twig->loadTemplate('/users/analysis.twig');
        return $template->render($output);

    }

    /**
     * Gets a list of Tag Names along with their frequencies for current user's listings in JSON format
     * @param $categoryIDArray (Optional - defaults to empty array => checks all category IDs. Array of CategoryIDs to match)
     * @return JSON
     */
    function getTagFrequenciesForListingsJSON($categoryIDArray = array())
    {
        $frequencies =  $this->model->getTagFrequenciesForListings($categoryIDArray);

        // Assuming this function will only be used for graphs with one categoryID!!
        $categoryID = $categoryIDArray[0];
        $tagNames = $this->model->getTagNamesFromCategory($categoryID);

        $results = array();

        // Extract TagID => Frequency pairs
        foreach($frequencies as $pair){
            $tagName = $pair["Name"];
            $frequency = $pair["Count"];

            $results[$tagName] = $frequency;
        }

        // Add any Tag Names from the category that weren't found in user's tags
        // Add them with frequency 0
        foreach($tagNames as $array){
            $tagName = $array["Name"];
            if(!(array_key_exists($tagName, $results))){
                $results[$tagName] = 0;
            }
        }

        ksort($results);
        return json_encode($results);
    }

    /**
     * Gets a list of Tag Names along with their frequencies for items the user has received in JSON format
     * @param $categoryIDArray (Optional - defaults to empty array => checks all category IDs. Array of CategoryIDs to match)
     * @return JSON
     */
    function getTagFrequenciesForTransactionsJSON($categoryIDArray = array())
    {
        $frequencies = $this->model->getTagFrequenciesForTransactions($categoryIDArray);

        // Assuming this function will only be used for graphs with one categoryID!!
        $categoryID = $categoryIDArray[0];

        $tagNames = $this->model->getTagNamesFromCategory($categoryID);
        $results = array();

        // Extract TagID => Frequency pairs
        foreach($frequencies as $pair){
            $tagName = $pair["Name"];
            $frequency = $pair["Count"];

            $results[$tagName] = $frequency;
        }

        // Add any Tag Names from the category that weren't found in user's tags
        // Add them with frequency 0
        foreach($tagNames as $array){
            $tagName = $array["Name"];
            if(!(array_key_exists($tagName, $results))){
                $results[$tagName] = 0;
            }
        }

        ksort($results);
        return json_encode($results);
    }


    /**
     * Get category names and IDs in JSON format
     * @return string
     */
    function getCategoryDetailsJSON(){
        $categories = $this->model->getCategoryNamesAndIDs();

        $results = array();
        foreach($categories as $categoryPair){
            $name = $categoryPair["Category_Name"];
            $id = $categoryPair["CategoryID"];

            $results[$id] = $name;
        }

        return json_encode($results);
    }


    /**
     * Returns array of top 5 names on items user has given away
     * @return array
     */
    function getTotalNameFrequenciesSending(){
        $frequencies = $this->model->getTotalNameFrequenciesSending();


        $names = array();

        foreach($frequencies as $array){
            $name = $array["Name"];
            $frequency = $array["Count"];

            $names[$name] = $frequency;
        }

        arsort($names);
        $limit = count($names) < 5 ? count($names) : 5;

        $results = array_slice($names, 0, $limit, true);

        return json_encode($results);
    }


    /**
     * Returns array of top 5 names on items user has received
     * @return array
     */
    function getTotalNameFrequenciesReceiving(){
        $frequencies = $this->model->getTotalNameFrequenciesReceiving();

        $names = array();

        foreach($frequencies as $array){
            $name = $array["Name"];
            $frequency = $array["Count"];

            $names[$name] = $frequency;
        }

        arsort($names);

        $limit = count($names) < 5 ? count($names) : 5;

        $results = array_slice($names, 0, $limit, true);

        return json_encode($results);
    }


    /**
     * Gets the most frequent item name given away
     * @return mixed
     */
    function getMostFrequentItemNameSent(){
        $names = json_decode($this->getTotalNameFrequenciesSending());
        print_r("ITEM NAMES");
        print_r($names);
        if(count($names) == 0){
            return null;
        }
        print_r("ARRAY_KEYS_NAMES");
        print_r(array_keys($names));
        return array_keys($names)[0];
    }


    /**
     * Gets the most frequent tag for Type category on items user gives away
     * @return mixed
     */
    function getMostFrequentTypeTagSent(){
        // Get most frequent Tags for the Type category
        $tags = $this->getTagFrequenciesForListingsJSON(array(1));
        print_r("TAGS");
        print_r($tags);
        if(count($tags) == 0){
            return null;
        }
        arsort($tags); // Sort by tag frequency

        print_r("ARRAY_KEYS_TAGS");
        print_r(array_keys($tags));
        return array_keys($tags)[0];
    }
}