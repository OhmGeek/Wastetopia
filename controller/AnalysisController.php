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
        // Get config
        $CurrentConfig = new CurrentConfig();
        $config = $CurrentConfig->getAll();

        //Create array for Twig file
        $output = array(
            "config" => $config
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

        $results = array();

        // Extract TagID => Frequency pairs
        foreach($frequencies as $pair){
            $tagName = $pair["Name"];
            $frequency = $pair["Count"];

            $results[$tagName] = $frequency;
        }

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

        $results = array();

        // Extract TagID => Frequency pairs
        foreach($frequencies as $pair){
            $tagName = $pair["Name"];
            $frequency = $pair["Count"];

            $results[$tagName] = $frequency;
        }

        return json_encode($results);
    }


    /**
     * Get category names and IDs in JSON format
     * @return string
     */
    function getCategoryDetailsJSON(){
        $categories = $this->model->getCategoryNamesAndIDs();

        return json_encode($categories);
    }
}