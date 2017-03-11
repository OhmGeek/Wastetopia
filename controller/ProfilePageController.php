<?php

/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 03/03/2017
 * Time: 11:24
 */
class ProfilePageController
{

    /**
     * ProfilePageController constructor.
     */
    public function __construct()
    {
        $this->model = new ProfilePageModel(); //Need to include

        //Load Twig environment
        $loader = new Twig_Loader_Filesystem('../view/');
        $this->twig = new Twig_Environment($loader);
    }


    function generatePage()
    {
        //Get user details
        $userDetails = $this->model->getUserDetails();

        $user = array();
        $user["forename"] = $userDetails["Forename"];
        $user["surname"] = $userDetails["Surname"];
        $user["email"] = $userDetails["Email_Address"];
        //$user["popularityRating"] = $userDetails["Mean_Rating_Percent"];


        //Get ProfilePage twig file and display it
        $template = $this->twig->loadTemplate("TWIG_FILE");
        print($template->render(array("user"=>$user)));
    }


}