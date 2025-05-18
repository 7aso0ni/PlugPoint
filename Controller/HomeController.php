<?php

namespace Controller;
use Model\UserModel;
require_once 'Model/UserModel.php';

class HomeController
{
    public function index()
    {
        $title = "Welcome Home";
       

        //  load the content of the home page and store it in the content variable
        // so it can be loaded in the layout page
        ob_start();
        require 'View/home/main.php';
        $content = ob_get_clean();

        require 'View/layouts/main.php';
    }
}