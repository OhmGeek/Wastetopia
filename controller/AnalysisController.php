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


/**
 * Class AnalysisController - Used to analyse the user's listings and transactions
 * @package Wastetopia\Controller
 */
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
        $sendingFrequencies = json_decode($this->getTotalNameFrequenciesSending(),true);

        print_r("Sending: ");
        print_r($sendingFrequencies);

        $sendingNames = array();

        // Extract name and frequency, place in associative array
        foreach($sendingFrequencies as $key=>$value){
            $details = array();
            $details["name"] = $key;
            $details["frequency"] = $value;

            array_push($sendingNames, $details);
        }

        // Same for receiving
        $receivingFrequencies = json_decode($this->getTotalNameFrequenciesReceiving(),true);

        print_r("Receiving: ");
        print_r($receivingFrequencies);

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
        $frequencies =  $this->model->getTagFrequenciesForListings(null, $categoryIDArray);

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
        error_log("Sent: ".$results);
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
        error_log("Received: ".$results);
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

            if($name !== "Other"){
                $results[$id] = $name;
            }
        }

        return json_encode($results);
    }


    /**
     * Returns array of top 5 names on items user has given away
     * @return array
     */
    function getTotalNameFrequenciesSending($userID = null){
        $frequencies = $this->model->getTotalNameFrequenciesSending($userID);


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

        // Limit <= 5, can't exceed size of $names
        $limit = count($names) < 5 ? count($names) : 5;

        $results = array_slice($names, 0, $limit, true);

        return json_encode($results);
    }


    /**
     * Gets the most frequent item name given away
     * @return mixed
     */
    function getMostFrequentItemNameSent(){
        $names = json_decode($this->getTotalNameFrequenciesSending(), true);

        if(count($names) == 0){
            return "";
        }

        return array_keys($names)[0];
    }


    /**
     * Gets the most frequent tag for Type category on items user gives away
     * @return mixed
     */
    function getMostFrequentTypeTagSent(){
        // Get most frequent Tags for the Type category
        $tags = json_decode($this->getTagFrequenciesForListingsJSON(array(1)), true);

        if(count($tags) == 0){
            return "";
        }
        arsort($tags); // Sort by tag frequency

        $mostFrequent = array_keys($tags)[0];
        
        if($tags[$mostFrequent] > 0){
            return $mostFrequent;
        }else{
            return "";
        }
        
    }



    /**
     * Returns an array of the number of items given in the last 4 months
     */
    function get4MonthSendArray(){
        $year = date('Y');
        $month1 = date('n');
        $month2 = date('n')-1;
        $month3 = date('n')-2;
        $month4 = date('n')-3;
        $send1 = $this->model->getNumberOfCompletedGiving($year, $month1, 1);
        $send2 = $this->model->getNumberOfCompletedGiving($year, $month2, 1);
        $send3 = $this->model->getNumberOfCompletedGiving($year, $month3, 1);
        $send4 = $this->model->getNumberOfCompletedGiving($year, $month4, 1);
        $final = array($send1,$send2,$send3,$send4);
        return json_encode($final);
    }


    /**
     * Returns an array of the number of items given in the last 4 months
     */
    function get4MonthReceiveArray(){
        $year = date('Y');
        $month1 = date('n');
        $month2 = date('n')-1;
        $month3 = date('n')-2;
        $month4 = date('n')-3;
        $send1 = $this->model->getNumberOfCompletedReceived($year, $month1, 1);
        $send2 = $this->model->getNumberOfCompletedReceived($year, $month2, 1);
        $send3 = $this->model->getNumberOfCompletedReceived($year, $month3, 1);
        $send4 = $this->model->getNumberOfCompletedReceived($year, $month4, 1);
        $final = array($send1,$send2,$send3,$send4);
        return json_encode($final);
    }
}
