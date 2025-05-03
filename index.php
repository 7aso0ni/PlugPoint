<?php
/* ───────────────────────── CONFIG  ───────────────────────── */
define('DB_HOST', 'localhost');
define('DB_NAME', 'mvc');
define('DB_USER', 'admin');
define('DB_PASSWORD', 'admin');

/* ───────────────────────── AUTOLOAD / REQUIRE ────────────── */
require_once 'Controller/HomeController.php';
require_once 'Controller/AuthController.php';
require_once 'Controller/AccountController.php';
require_once 'Controller/ChargePointController.php';
require_once 'Controller/BookingController.php';

/* ───────────────────────── CONTROLLER INSTANCES ──────────── */
$home = new Controller\HomeController();
$auth = new Controller\AuthController();
$account = new Controller\AccountController();
$chargers = new Controller\ChargePointController();
$booking = new Controller\BookingController();

/* ───────────────────────── ROUTING  ──────────────────────── */
$route = $_GET['route'] ?? 'home';

switch ($route) {
    case 'login':
        $auth->login();
        break;
    case 'signup':
        $auth->signup();
        break;
    case 'logout':
        $auth->logout();
        break;
    case 'account':
        $account->showAccountPage();
        break;
    case 'update_profile':
        $account->updateUser();
        break;
    case 'chargepoints':
        $chargers->index();
        break;
    case 'chargepoints/filter':
        $chargers->filter();
        break;
    case 'chargepoints/details':
        $chargers->details();
        break;
    case 'booking_form':
        $booking->BookingForm();
        break;
    case 'create_booking':
        $booking->CreateBooking();
        break;
    case 'booking_confirmation':
        $booking->BookingConfirmation();
        break;
    case 'cancel_booking':
        $booking->CancelBooking();
        break;
    case 'my_bookings':
        $booking->MyBookings();
        break;
    case 'get_available_slots':
        $booking->GetAvailableSlots();
        break;
    case 'api/nearby_stations':
        $api->nearbyStations();
        break;
    case 'home':
    default:
        $home->index();
        break;
}
