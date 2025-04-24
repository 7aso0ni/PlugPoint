<?php

require_once 'Controller/HomeController.php';
require_once 'Controller/AuthController.php';

$route = $_GET['route'] ?? 'home';

define('DB_HOST', 'localhost');
define('DB_NAME', 'mvc');
define('DB_USER', 'admin');
define('DB_PASSWORD', 'admin');

$home = new Controller\HomeController();
$auth =  new Controller\AuthController();

switch ($route) {
    case 'login':
        $auth->login();
        break;
    case 'signup':
        $auth->signup();
        break;
    case 'home':
    default:
        $home->index();
}