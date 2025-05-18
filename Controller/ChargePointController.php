<?php

namespace Controller;

require_once 'Model/ChargePointModel.php';
require_once 'Model/UserModel.php';
require_once 'Model/BookingModel.php';
require_once __DIR__ . '/BaseController.php';

use Model\ChargePointModel;
use Model\UserModel;
use Model\BookingModel;

class ChargePointController extends \Controller\BaseController
{

    private $chargePointModel;
    private $userModel;
    private $bookingModel;

    public function __construct()
    {
        $this->chargePointModel = new ChargePointModel();
        $this->userModel = new UserModel();
        $this->bookingModel = new BookingModel();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /* ─────────────────────────────────────────────────────────── */
    /* LIST PAGE                                                  */
    /* ─────────────────────────────────────────────────────────── */

    //    public function index()
//    {
//        $title = 'EV Charging Stations';
//
//        $perPage = 8;
//
//        // Get all charge points (both available and unavailable) for initial page load
//        // Use getFilteredChargePoints with null availability to show all
//        $chargePoints = $this->chargePointModel->getFilteredChargePoints(
//            '', // No search term
//            1.00, // High max price to show all
//            null, // Null means show both available and unavailable
//            $perPage,
//            0 // First page
//        );
//
//        $totalCount = $this->chargePointModel->getFilteredChargePointsCount(
//            '', // No search term
//            1.00, // High max price to show all
//            null // Null means show both available and unavailable
//        );
//
//        $totalPages = (int) ceil($totalCount / $perPage);
//
//        $initialData = [
//            'chargePoints' => $chargePoints,
//            'pagination' => [
//                'current_page' => 1,
//                'total_pages' => $totalPages,
//                'per_page' => $perPage,
//                'total_count' => $totalCount,
//            ],
//        ];
//
//        /* view */
//        ob_start();
//        require 'View/shop/main.php';
//        $content = ob_get_clean();
//
//        /* layout */
//        require 'View/layouts/main.php';
//    }
    public function index()
    {


        $title = 'EV Charging Stations';

        // Fetch all charge points without filtering by price, availability, or pagination
        $chargePoints = $this->chargePointModel->getFilteredChargePoints(
            '', // No search term
            999999.0, // Very high max price to include all
            null, // Include both available and unavailable
            1000, // High limit to show all at once
            0           // Start from first
        );

        $totalCount = count($chargePoints);
        $totalPages = 1; // Since we’re loading everything

        $initialData = [
            'chargePoints' => $chargePoints,
            'pagination' => [
                'current_page' => 1,
                'total_pages' => $totalPages,
                'per_page' => $totalCount,
                'total_count' => $totalCount,
            ],
        ];

        ob_start();
        require 'View/shop/main.php';
        $content = ob_get_clean();

        require 'View/layouts/main.php';
    }

    /* ─────────────────────────────────────────────────────────── */
    /* AJAX FILTER END-POINT                                      */
    /* ─────────────────────────────────────────────────────────── */

    public function filter()
    {
        // Get parameters from both POST and GET
        $search = $_POST['search'] ?? '';
        $maxPrice = (float) ($_POST['max_price'] ?? 0.50);
        $available = $_POST['available'] !== '' ? (int) $_POST['available'] : null;
        $page = (int) ($_POST['page'] ?? 1);
        $perPage = 8;
        $offset = ($page - 1) * $perPage;
        $forceReload = isset($_GET['force_reload']) && $_GET['force_reload'] == 1;

        // If force reload is requested, show all stations without pagination
        if ($forceReload) {
            $chargePoints = $this->chargePointModel->getFilteredChargePoints(
                $search,
                $maxPrice,
                null, // Show all stations
                1000, // Large limit to show all
                0
            );
            $totalCount = count($chargePoints);
            $totalPages = 1;
        } else {
            $chargePoints = $this->chargePointModel->getFilteredChargePoints(
                $search,
                $maxPrice,
                $available,
                $perPage,
                $offset
            );
            $totalCount = $this->chargePointModel->getFilteredChargePointsCount(
                $search,
                $maxPrice,
                $available
            );
            $totalPages = (int) ceil($totalCount / $perPage);
        }
        // The totalCount and totalPages are already set in the if/else block above

        header('Content-Type: application/json');
        echo json_encode([
            'chargePoints' => $chargePoints,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_count' => $totalCount,
            ],
        ]);
        exit;
    }

    /* ─────────────────────────────────────────────────────────── */
    /* DETAILS PAGE                                               */
    /* ─────────────────────────────────────────────────────────── */

    public function details()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $title = 'Charging Station Details';
        $cp = $this->chargePointModel->getChargePointWithOwner($id);

        if (!$cp) {
            header('Location: index.php?route=chargepoints');
            exit;
        }

        // Get booking slots regardless of availability status
        $start = date('Y-m-d');
        $end = date('Y-m-d', strtotime('+7 days'));
        $bookings = $this->bookingModel->getBookedSlots($id, $start, $end);

        /* view */
        ob_start();
        require 'View/shop/detail.php';
        $content = ob_get_clean();

        /* layout */
        require 'View/layouts/main.php';
    }

    public function addCharger()
    {
        $title = 'Add New Charger';

        ob_start();
        require 'View/ChargePoints/add_charger.php';  // the inner view content
        $content = ob_get_clean();

        require 'View/layouts/main.php'; // wraps it in the layout (with navbar)
    }

    public function saveCharger()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $location = trim($_POST['location'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $available_from = $_POST['available_from'] ?? '';
            $available_to = $_POST['available_to'] ?? '';
            $latitude = floatval($_POST['latitude'] ?? 0);
            $longitude = floatval($_POST['longitude'] ?? 0);
            $user_id = $_SESSION['user']['id'] ?? null;

            // Validation
            $errors = [];

            if (empty($location)) {
                $errors[] = "Location is required";
            }

            if ($price <= 0) {
                $errors[] = "Price must be greater than zero";
            }

            if ($latitude == 0 || $longitude == 0) {
                $errors[] = "Please select a valid location on the map";
            }

            if (!$user_id) {
                $errors[] = "You must be logged in to add a charger";
                header('Location: index.php?route=login');
                exit();
            }

            // Check if user has permission to add chargers (must be a charger owner)
            if (!isset($_SESSION['user']['role_id']) || $_SESSION['user']['role_id'] != 2) {
                $errors[] = "You don't have permission to add chargers";
                // Keep user on the add charger page with error message
                $_SESSION['errors'] = $errors;
                header('Location: index.php?route=homeowner/add_charger');
                exit();
            }

            // If there are validation errors, redirect back with error messages
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: index.php?route=homeowner/add_charger');
                exit();
            }

            // Handle optional image upload
            $image_path = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_dir = "uploads/";
                
                // Create directory if it doesn't exist with proper permissions
                if (!is_dir($target_dir)) {
                    if (!mkdir($target_dir, 0777, true)) {
                        error_log("Failed to create directory: " . $target_dir);
                        $_SESSION['errors'] = ["Failed to create upload directory"];
                        header('Location: index.php?route=homeowner/add_charger');
                        exit();
                    }
                    chmod($target_dir, 0777); // Ensure directory is writable
                }

                // Get file extension
                $file_name = basename($_FILES['image']['name']);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                // Validate file type
                $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($file_ext, $allowed_exts)) {
                    error_log("Invalid file type: " . $file_ext);
                    $_SESSION['errors'] = ["Only JPG, PNG and GIF images are allowed"];
                    header('Location: index.php?route=homeowner/add_charger');
                    exit();
                }

                // Validate file size (max 5MB)
                if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    error_log("File too large: " . $_FILES['image']['size']);
                    $_SESSION['errors'] = ["Image size should not exceed 5MB"];
                    header('Location: index.php?route=homeowner/add_charger');
                    exit();
                }
                
                // Validate file is an actual image
                $image_info = @getimagesize($_FILES['image']['tmp_name']);
                if (!$image_info) {
                    error_log("Not a valid image file");
                    $_SESSION['errors'] = ["Not a valid image file"];
                    header('Location: index.php?route=homeowner/add_charger');
                    exit();
                }

                // Generate unique filename
                $unique_name = time() . '_' . uniqid() . '.' . $file_ext;
                $target_file = $target_dir . $unique_name;

                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_path = $target_file;
                    chmod($target_file, 0644); // Make file readable
                    error_log("Image uploaded successfully to: " . $target_file);
                } else {
                    error_log("Failed to move uploaded file from {$_FILES['image']['tmp_name']} to {$target_file}");
                    $_SESSION['errors'] = ["Failed to upload image"];
                    header('Location: index.php?route=homeowner/add_charger');
                    exit();
                }
            }

            try {
                $db = new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $stmt = $db->prepare("
                INSERT INTO ChargePoints 
                    (owner_id, address, latitude, longitude, price_per_kWh, availability, image_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ");

                // Availability is set to 1 (available) by default
                $availability = 1;

                $stmt->execute([
                    $user_id,
                    $location,
                    $latitude,
                    $longitude,
                    $price,
                    $availability,
                    $image_path
                ]);

                $_SESSION['success'] = "Charger added successfully!";
                header('Location: index.php?route=homeowner/my_chargers');
                exit();
            } catch (\PDOException $e) {
                $_SESSION['errors'] = ["Database error: " . $e->getMessage()];
                header('Location: index.php?route=homeowner/add_charger');
                exit();
            }
        } else {
            // If not POST request, redirect to the form
            header('Location: index.php?route=homeowner/add_charger');
            exit();
        }
    }

    public function myChargers()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?route=login');
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $title = 'My Chargers';
        $myChargers = $this->chargePointModel->getChargePointsByOwner($userId);

        // Fetch pending bookings for each charger
        $chargerBookings = [];
        foreach ($myChargers as $charger) {
            $pendingBookings = $this->bookingModel->getPendingBookingsByChargePoint($charger['id']);
            $chargerBookings[$charger['id']] = $pendingBookings;
        }

        ob_start();
        require 'View/ChargePoints/my_chargers.php';
        $content = ob_get_clean();

        require 'View/layouts/main.php';
    }

    public function editCharger()
    {
        if (empty($_SESSION['user'])) {
            header('Location: index.php?route=login');
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: index.php?route=homeowner/my_chargers');
            exit;
        }

        $charger = $this->chargePointModel->getChargerById($id);

        // Ensure the logged-in user is the owner
        if (!$charger || $charger['owner_id'] != $userId) {
            echo "Unauthorized access.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $address = $_POST['address'] ?? '';
            $price = floatval($_POST['price'] ?? 0);
            $availability = isset($_POST['availability']) ? (int) $_POST['availability'] : 0;

            // Handle image upload if a new file is provided
            $image_url = $charger['image_url'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_dir = "uploads/";
                
                // Create directory if it doesn't exist with proper permissions
                if (!is_dir($target_dir)) {
                    if (!mkdir($target_dir, 0777, true)) {
                        error_log("Failed to create directory: " . $target_dir);
                        $_SESSION['errors'] = ["Failed to create upload directory"];
                        header('Location: index.php?route=homeowner/edit_charger?id=' . $id);
                        exit();
                    }
                    chmod($target_dir, 0777); // Ensure directory is writable
                }

                // Get file extension
                $file_name = basename($_FILES['image']['name']);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                // Validate file type
                $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($file_ext, $allowed_exts)) {
                    error_log("Invalid file type: " . $file_ext);
                    $_SESSION['errors'] = ["Only JPG, PNG and GIF images are allowed"];
                    header('Location: index.php?route=homeowner/edit_charger?id=' . $id);
                    exit();
                }

                // Validate file size (max 5MB)
                if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    error_log("File too large: " . $_FILES['image']['size']);
                    $_SESSION['errors'] = ["Image size should not exceed 5MB"];
                    header('Location: index.php?route=homeowner/edit_charger?id=' . $id);
                    exit();
                }
                
                // Validate file is an actual image
                $image_info = @getimagesize($_FILES['image']['tmp_name']);
                if (!$image_info) {
                    error_log("Not a valid image file");
                    $_SESSION['errors'] = ["Not a valid image file"];
                    header('Location: index.php?route=homeowner/edit_charger?id=' . $id);
                    exit();
                }

                // Generate unique filename
                $unique_name = time() . '_' . uniqid() . '.' . $file_ext;
                $target_file = $target_dir . $unique_name;

                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_url = $target_file;
                    chmod($target_file, 0644); // Make file readable
                    error_log("Image uploaded successfully to: " . $target_file);
                } else {
                    error_log("Failed to move uploaded file from {$_FILES['image']['tmp_name']} to {$target_file}");
                    $_SESSION['errors'] = ["Failed to upload image"];
                    header('Location: index.php?route=homeowner/edit_charger?id=' . $id);
                    exit();
                }
            }

            // Update charger
            $this->chargePointModel->updateCharger($id, $address, $price, $availability, $image_url);
            header('Location: index.php?route=homeowner/my_chargers');
            exit;
        }

        ob_start();
        require 'View/ChargePoints/edit_charger.php';
        $content = ob_get_clean();

        require 'View/layouts/main.php';
    }

    public function deleteCharger()
    {
        if (empty($_SESSION['user'])) {
            header('Location: index.php?route=login');
            exit;
        }

        $id = $_POST['id'] ?? null;

        if ($id) {
            $this->chargePointModel->deleteCharger($id);
        }

        header('Location: index.php?route=homeowner/my_chargers');
        exit;
    }

}
