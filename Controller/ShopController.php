<?php

namespace Controller;

class ShopController
{
    public function index() {
        $title = "Shop";

        ob_start();
        require 'View/shop/main.php';
        $content = ob_get_clean();

        require 'View/layouts/main.php';
    }
}