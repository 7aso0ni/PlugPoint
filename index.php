<?php

require_once 'Controller/HomeController.php';
require_once 'Controller/AuthController.php';
require_once 'Controller/ShopController.php';
require_once 'Controller/DashboardController.php';
require_once 'Controller/AccountController.php';

$route = $_GET['route'] ?? 'home';

define('DB_HOST', 'localhost');
define('DB_NAME', 'mvc');
define('DB_USER', 'admin');
define('DB_PASSWORD', 'admin');

$home = new Controller\HomeController();
$auth = new Controller\AuthController();
$shop = new Controller\ShopController();
$dashboard = new Controller\DashboardController();
$account = new Controller\AccountController();

switch ($route) {
    case 'login':
        $auth->login();
        break;
    case 'signup':
        $auth->signup();
        break;
    case 'shop':
        $shop->index();
        break;
    case "dashboard":
        $dashboard->index();
        break;
    case "cancelRental":
        $dashboard->cancelRental();
        break;
    case "account":
        $account->showAccountPage();
        break;
    case "update_profile":
        $account->updateUser();
        break;
    case 'logout':
        $auth->logout();
        break;
    case 'home':
    default:
        $home->index();
}