<?php

namespace Controller;

class DashboardController
{
    public function index() {
        $title =  "Dashboard";

        ob_start();
        require "View/dashboard/main.php";
        $content = ob_get_clean();

        require "View/layouts/main.php";
    }
}