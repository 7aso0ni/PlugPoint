<?php

/* ───────────────────────── CONFIG  ───────────────────────── */
ini_set('display_errors', 1); // temporarily enable for browser
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Try to log to file, but don't fail if permissions don't allow it
try {
    $logFile = __DIR__ . '/php_error_log.txt';
    if (is_writable($logFile) || (!file_exists($logFile) && is_writable(dirname($logFile)))) {
        file_put_contents($logFile, "Started index.php\n", FILE_APPEND);
    }

    register_shutdown_function(function () {
        $error = error_get_last();
        if ($error && $error['type'] === E_ERROR) {
            $logFile = __DIR__ . '/php_error_log.txt';
            if (is_writable($logFile) || (!file_exists($logFile) && is_writable(dirname($logFile)))) {
                file_put_contents($logFile, "Fatal Error: " . print_r($error, true), FILE_APPEND);
            }
        }
    });
} catch (Exception $e) {
    // Silently continue if logging fails
}

//define('DB_HOST', '20.126.5.244');
define('DB_HOST', 'localhost');
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
    case 'api/users':
        $api->users();
        break;

    // Admin routes
    case 'admin/dashboard':
        $admin->dashboard();
        break;
    case 'admin/users':
        $admin->users();
        break;
    case 'admin/user_add':
        $admin->addUser();
        break;
    case 'admin/user_update':
        $admin->updateUser();
        break;
    case 'admin/user_delete':
        $admin->deleteUser();
        break;
    case 'admin/charge_points':
        $admin->chargePoints();
        break;
    case 'admin/add_charge_point_form':
        $admin->showAddChargePoint();
        break;
    case 'admin/add_charge_point':
        $admin->addChargePoint();
        break;
    case 'admin/charge_point_update':
        $admin->updateChargePoint();
        break;
    case 'admin/charge_point_delete':
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