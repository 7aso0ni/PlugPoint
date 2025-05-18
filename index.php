<?php

/* ───────────────────────── CONFIG  ───────────────────────── */
//define('DB_HOST', 'localhost');
//define('DB_NAME', 'mvc');
//define('DB_USER', 'admin');
//define('DB_PASSWORD', 'admin');
//


define('DB_HOST', '20.126.5.244');
// define('DB_HOST', 'localhost');
define('DB_NAME', 'db202201044');
define('DB_USER', 'u202201044');
define('DB_PASSWORD', 'u202201044');


/* ───────────────────────── AUTOLOAD / REQUIRE ────────────── */
require_once 'Controller/HomeController.php';
require_once 'Controller/AuthController.php';
require_once 'Controller/AccountController.php';
require_once 'Controller/ChargePointController.php';
require_once 'Controller/BookingController.php';
require_once 'Controller/AdminController.php';
require_once 'Controller/ApiController.php'; // Add API controller

/* ───────────────────────── CONTROLLER INSTANCES ──────────── */
$home = new Controller\HomeController();
$auth = new Controller\AuthController();
$account = new Controller\AccountController();
$chargers = new Controller\ChargePointController();
$booking = new Controller\BookingController();
$admin = new Controller\AdminController();
$api = new Controller\ApiController(); // Create API controller instance

/* ───────────────────────── ROUTING  ──────────────────────── */
$route = $_GET['route'] ?? 'home';

switch ($route) {
    // User-facing routes
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
    case 'booking/approve':
        $booking->ApproveBooking();
        break;
    case 'booking/decline':
        $booking->DeclineBooking();
        break;

    // API routes
    case 'api/nearby_stations':
        $api->nearbyStations();
        break;
    case 'api/users': // Add this new API route for user search
        $api->users();
        break;

    // Admin routes
    case 'admin/dashboard':
        $admin->dashboard();
        break;
    case 'admin/users':
        $admin->users();
        break;
    case 'admin/user_add':  // Changed to match the controller methods
        $admin->addUser();
        break;
    case 'admin/user_update':  // Changed to match the controller methods
        $admin->updateUser();
        break;
    case 'admin/user_delete':  // Changed to match the controller methods
        $admin->deleteUser();
        break;
    case 'admin/charge_points':
        $admin->chargePoints();
        break;
    case 'admin/add_charge_point_form':
        $admin->showAddChargePoint();
        break;
    case 'admin/add_charge_point':  // Changed to match the controller methods
        $admin->addChargePoint();
        break;
    case 'admin/charge_point_update':  // Changed to match the controller methods
        $admin->updateChargePoint();
        break;
    case 'admin/charge_point_delete':  // Changed to match the controller methods
        $admin->deleteChargePoint();
        break;
    case 'admin/bookings':
        $admin->bookings();
        break;
    case 'admin/booking_details':
        $admin->bookingDetails();
        break;
    case 'admin/change_booking_status':
        $admin->changeBookingStatus();
        break;
    case 'admin/reports':
        $admin->reports();
        break;
    case 'homeowner/add_charger':
        $chargers->addCharger();
        break;
    case 'homeowner/save_charger':
        $chargers->saveCharger();
        break;
    case 'homeowner/my_chargers':
        $controller = new Controller\ChargePointController();
        $controller->myChargers();
        break;
    case 'homeowner/edit_charger':
        (new \Controller\ChargePointController())->editCharger();
        break;

    case 'homeowner/delete_charger':
        $controller = new Controller\ChargePointController();
        $controller->deleteCharger(); // handles $_POST['id']
        break;
    case 'home':
    default:
        $home->index();
        break;
}