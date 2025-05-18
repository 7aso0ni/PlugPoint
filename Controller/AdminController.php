<?php

namespace Controller;

use Model\UserModel;
use Model\ChargePointModel;
use Model\BookingModel;

class AdminController extends BaseController
{
    private $userModel;
    private $chargePointModel;
    private $bookingModel;

    public function __construct()
    {
        parent::__construct();

        $this->userModel = new UserModel();
        $this->chargePointModel = new ChargePointModel();
        $this->bookingModel = new BookingModel();
    }

    /**
     * Dashboard action
     */
    public function dashboard()
    {
        // // Check if user is admin, redirect if not
        $this->checkAdminAccess();

        $title = "Admin Dashboard";

        // Get dashboard stats
        $stats = [
            'totalUsers' => $this->userModel->getTotalUsers(),
            'totalChargePoints' => $this->chargePointModel->getTotalChargePoints(),
            'pendingBookings' => $this->bookingModel->getBookingCountByStatus('Pending'),
            'estimatedRevenue' => $this->bookingModel->getEstimatedMonthlyRevenue()
        ];

        // Get recent bookings
        $recentBookings = $this->bookingModel->getRecentBookingsWithDetails(5);

        ob_start();
        require 'View/admin/dashboard.php';
        $content = ob_get_clean();

        require 'View/layouts/admin.php';
    }

    /**
     * User management action
     */
    public function users()
    {
        $title = "User Management";

        // Get pagination parameters
        $currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $perPage = 10;
        $offset = ($currentPage - 1) * $perPage;

        // Get search parameter
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // Get users with pagination
        if (!empty($search)) {
            $users = $this->userModel->searchUsers($search, $perPage, $offset);
            $totalUsers = $this->userModel->getTotalSearchResults($search);
        } else {
            $users = $this->userModel->getAllUsers($perPage, $offset);
            $totalUsers = $this->userModel->getTotalUsers();
        }

        $totalPages = ceil($totalUsers / $perPage);

        ob_start();
        require 'View/admin/users.php';
        $content = ob_get_clean();

        require 'View/layouts/admin.php';
    }

    /**
     * Charge points management action
     */
    public function chargePoints()
    {
        $title = "Charge Point Management";

        // Get pagination parameters
        $currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $perPage = 10;
        $offset = ($currentPage - 1) * $perPage;

        // Get search parameter
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // Get charge points with pagination
        if (!empty($search)) {
            $chargePoints = $this->chargePointModel->searchChargePoints($search, $perPage, $offset);
            $totalChargePoints = $this->chargePointModel->getTotalSearchResults($search);
        } else {
            $chargePoints = $this->chargePointModel->getAllChargePointsWithOwners($perPage, $offset);
            $totalChargePoints = $this->chargePointModel->getTotalChargePoints();
        }

        // Get all owners for dropdown
        $owners = $this->userModel->getAllUsers();

        $totalPages = ceil($totalChargePoints / $perPage);

        ob_start();
        require 'View/admin/charge_points.php';
        $content = ob_get_clean();

        require 'View/layouts/admin.php';
    }

    /**
     * Bookings management action
     */
    public function bookings()
    {
        $title = "Booking Management";

        // Get filter parameters
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // Get pagination parameters
        $currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $perPage = 10;
        $offset = ($currentPage - 1) * $perPage;

        // Get bookings with filters and pagination
        $bookings = $this->bookingModel->getFilteredBookingsWithDetails($status, $search, $perPage, $offset);
        $totalBookings = $this->bookingModel->getTotalFilteredBookings($status, $search);

        $totalPages = ceil($totalBookings / $perPage);

        ob_start();
        require 'View/admin/bookings.php';
        $content = ob_get_clean();

        require 'View/layouts/admin.php';
    }

    /**
     * View booking details
     */
    public function bookingDetails()
    {
        $title = "Booking Details";

        // Get booking ID
        $bookingId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if (!$bookingId) {
            $this->setMessage('error', 'Invalid booking ID');
            $this->redirect('admin/bookings');
        }

        // Get booking details
        $booking = $this->bookingModel->getBookingWithDetails($bookingId);

        if (!$booking) {
            $this->setMessage('error', 'Booking not found');
            $this->redirect('admin/bookings');
        }

        // Get the full charge point details
        $chargePoint = $this->chargePointModel->getChargePointById($booking['charge_point_id']);

        ob_start();
        require 'View/admin/booking_details.php';
        $content = ob_get_clean();

        require 'View/layouts/admin.php';
    }

    /**
     * Change booking status
     */
    public function changeBookingStatus()
    {
        // Get parameters
        $bookingId = isset($_POST['booking_id']) ? (int) $_POST['booking_id'] : 0;
        $status = isset($_POST['status']) ? trim($_POST['status']) : '';
        $note = isset($_POST['note']) ? trim($_POST['note']) : '';

        if (!$bookingId || empty($status)) {
            $this->setMessage('error', 'Invalid parameters');
            $this->redirect('admin/bookings');
        }

        // Update booking status
        $result = $this->bookingModel->updateBookingStatus($bookingId, $status);

        if ($result) {
            $this->setMessage('success', 'Booking status updated successfully');

            // Add note if provided
            if (!empty($note)) {
                $this->bookingModel->addBookingNote($bookingId, $note, $_SESSION['user']['id']);
            }
        } else {
            $this->setMessage('error', 'Failed to update booking status');
        }

        $this->redirect('admin/booking_details&id=' . $bookingId);
    }

    /**
     * Add new user action
     */
    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/users');
        }

        // Get form data
        $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
        $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $roleId = isset($_POST['role_id']) ? (int) $_POST['role_id'] : 1;

        // Validate required fields
        if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password)) {
            $this->setMessage('error', 'All fields are required');
            $this->redirect('admin/users');
        }

        // Check if user already exists
        if ($this->userModel->doesUserExist($email)) {
            $this->setMessage('error', 'User with this email already exists');
            $this->redirect('admin/users');
        }

        // Create user data
        $userData = [
            'name' => $firstName . ' ' . $lastName,
            'email' => $email,
            'phone' => $phone,
            'password' => $password, // Password will be hashed in UserModel::createUser
            'role_id' => $roleId,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Create user
        $result = $this->userModel->createUser($userData);

        if ($result) {
            $this->setMessage('success', 'User created successfully');
        } else {
            $this->setMessage('error', 'Failed to create user');
        }

        $this->redirect('admin/users');
    }

    /**
     * Update user action
     */
    public function updateUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/users');
        }

        // Get form data
        $userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $roleId = isset($_POST['role_id']) ? (int) $_POST['role_id'] : 1;

        // Validate required fields
        if (empty($userId) || empty($name) || empty($email) || empty($phone)) {
            $this->setMessage('error', 'Required fields cannot be empty');
            $this->redirect('admin/users');
        }

        // Get existing user
        $existingUser = $this->userModel->getUserById($userId);
        if (!$existingUser) {
            $this->setMessage('error', 'User not found');
            $this->redirect('admin/users');
        }

        // Check email uniqueness if changed
        if ($email !== $existingUser['email'] && $this->userModel->doesUserExist($email)) {
            $this->setMessage('error', 'Email already in use by another user');
            $this->redirect('admin/users');
        }

        // Prepare update data
        $userData = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role_id' => $roleId
        ];

        // Add password if provided
        if (!empty($password)) {
            $userData['password'] = $password; // Will be hashed in the model
        }

        // Update user
        $result = $this->userModel->updateUser($userId, $userData);

        if ($result) {
            $this->setMessage('success', 'User updated successfully');
        } else {
            $this->setMessage('error', 'Failed to update user');
        }

        $this->redirect('admin/users');
    }

    /**
     * Delete user action
     */
    public function deleteUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/users');
        }

        // Get user ID
        $userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;

        if (!$userId) {
            $this->setMessage('error', 'Invalid user ID');
            $this->redirect('admin/users');
        }

        // Prevent self-deletion
        if ($userId == $_SESSION['user']['id']) {
            $this->setMessage('error', 'You cannot delete your own account');
            $this->redirect('admin/users');
        }

        // Delete user
        $result = $this->userModel->deleteUser($userId);

        if ($result) {
            $this->setMessage('success', 'User deleted successfully');
        } else {
            $this->setMessage('error', 'Failed to delete user');
        }

        $this->redirect('admin/users');
    }

    /**
     * Show add charge point form with map
     */
    public function showAddChargePoint()
    {
        // Check if user is admin, redirect if not
        $this->checkAdminAccess();

        $title = "Add New Charging Station";

        // Get all owners for dropdown
        $owners = $this->userModel->getAllUsers();

        ob_start();
        require 'View/admin/add_charge_point.php';
        $content = ob_get_clean();

        require 'View/layouts/admin.php';
    }

    /**
     * Add charge point action
     */
    public function addChargePoint()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/charge_points');
        }

        // Get form data
        $address = isset($_POST['address']) ? trim($_POST['address']) : '';
        $latitude = isset($_POST['latitude']) ? (float) $_POST['latitude'] : 0;
        $longitude = isset($_POST['longitude']) ? (float) $_POST['longitude'] : 0;
        $pricePerKwh = isset($_POST['price_per_kWh']) ? (float) $_POST['price_per_kWh'] : 0;
        $availability = isset($_POST['availability']) ? (int) $_POST['availability'] : 0;
        $ownerId = isset($_POST['owner_id']) ? (int) $_POST['owner_id'] : 0;
        $imageUrl = isset($_POST['image_url']) ? trim($_POST['image_url']) : 'images/chargepoint-default.jpg';

        // Validate required fields
        if (empty($address) || !$latitude || !$longitude || !$pricePerKwh || !$ownerId) {
            $this->setMessage('error', 'All fields are required');
            $this->redirect('admin/charge_points');
        }

        // Create charge point data
        $chargePointData = [
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'price_per_kWh' => $pricePerKwh,
            'availability' => $availability,
            'owner_id' => $ownerId,
            'image_url' => $imageUrl,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Upload image if provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadedFile = $_FILES['image'];
            $imageUrl = $this->uploadImage($uploadedFile);

            if ($imageUrl) {
                $chargePointData['image_url'] = $imageUrl;
            } else {
                $this->setMessage('error', 'Failed to upload image. Using default image.');
            }
        }

        // Create charge point
        $result = $this->chargePointModel->createChargePoint($chargePointData);

        if ($result) {
            $this->setMessage('success', 'Charging station created successfully');
        } else {
            $this->setMessage('error', 'Failed to create charging station');
        }

        $this->redirect('admin/charge_points');
    }

    /**
     * Update charge point action
     */
    public function updateChargePoint()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/charge_points');
        }

        // Get form data
        $cpId = isset($_POST['cp_id']) ? (int) $_POST['cp_id'] : 0;
        $address = isset($_POST['address']) ? trim($_POST['address']) : '';
        $latitude = isset($_POST['latitude']) ? (float) $_POST['latitude'] : 0;
        $longitude = isset($_POST['longitude']) ? (float) $_POST['longitude'] : 0;
        $pricePerKwh = isset($_POST['price_per_kWh']) ? (float) $_POST['price_per_kWh'] : 0;
        $availability = isset($_POST['availability']) ? (int) $_POST['availability'] : 0;
        $ownerId = isset($_POST['owner_id']) ? (int) $_POST['owner_id'] : 0;

        // Validate required fields
        if (!$cpId || empty($address) || !$latitude || !$longitude || !$pricePerKwh || !$ownerId) {
            $this->setMessage('error', 'All fields are required');
            $this->redirect('admin/charge_points');
        }

        // Get existing charge point
        $existingCp = $this->chargePointModel->getChargePointById($cpId);
        if (!$existingCp) {
            $this->setMessage('error', 'Charging station not found');
            $this->redirect('admin/charge_points');
        }

        // Prepare update data
        $chargePointData = [
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'price_per_kWh' => $pricePerKwh,
            'availability' => $availability,
            'owner_id' => $ownerId
        ];

        // Upload image if provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadedFile = $_FILES['image'];
            $imageUrl = $this->uploadImage($uploadedFile);

            if ($imageUrl) {
                $chargePointData['image_url'] = $imageUrl;
            } else {
                $this->setMessage('error', 'Failed to upload image. Using existing image.');
            }
        }

        // Update charge point
        $result = $this->chargePointModel->updateChargePoint($cpId, $chargePointData);

        if ($result) {
            $this->setMessage('success', 'Charging station updated successfully');
        } else {
            $this->setMessage('error', 'Failed to update charging station');
        }

        $this->redirect('admin/charge_points');
    }

    /**
     * Delete charge point action
     */
    public function deleteChargePoint()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/charge_points');
        }

        // Get charge point ID
        $cpId = isset($_POST['cp_id']) ? (int) $_POST['cp_id'] : 0;

        if (!$cpId) {
            $this->setMessage('error', 'Invalid charging station ID');
            $this->redirect('admin/charge_points');
        }

        // Check for existing bookings
        $hasBookings = $this->bookingModel->hasBookingsByChargePoint($cpId);

        if ($hasBookings) {
            $this->setMessage('error', 'Cannot delete charging station with existing bookings');
            $this->redirect('admin/charge_points');
        }

        // Delete charge point
        $result = $this->chargePointModel->deleteChargePoint($cpId);

        if ($result) {
            $this->setMessage('success', 'Charging station deleted successfully');
        } else {
            $this->setMessage('error', 'Failed to delete charging station');
        }

        $this->redirect('admin/charge_points');
    }

    /**
     * Upload image helper method
     */
    private function uploadImage($file)
    {
        // Basic validation
        if (!isset($file['tmp_name']) || !isset($file['name'])) {
            return false;
        }

        // Get file info
        $fileName = basename($file['name']);
        $targetDir = "uploads/";

        // Create directory if it doesn't exist
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0777, true)) {
                return false;
            }
        }

        // Generate unique filename
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $uniqueName = time() . '_' . uniqid() . '.' . $fileExt;
        $targetFilePath = $targetDir . $uniqueName;

        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExt, $allowedTypes)) {
            return false;
        }

        // Validate file size
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            return false;
        }

        // Validate file is an actual image
        if (!getimagesize($file['tmp_name'])) {
            return false;
        }

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            return false;
        }

        // Make file readable
        chmod($targetFilePath, 0644);

        return $targetFilePath;
    }

    /**
     * Reports page
     */
    public function reports()
    {
        $title = "Reports";

        // Get monthly stats for the last 6 months
        $months = 6;
        $monthlyStats = $this->bookingModel->getMonthlyStats($months);

        // Get user growth
        $userGrowth = $this->userModel->getUserGrowthStats($months);

        // Get top charge points
        $topChargePoints = $this->bookingModel->getTopChargePoints();

        ob_start();
        require 'View/admin/reports.php';
        $content = ob_get_clean();

        require 'View/layouts/admin.php';
    }

    /**
     * Helper method to check if user is admin
     */
    protected function checkAdminAccess()
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role_id'] ?? 0) !== 1) {
            $this->setMessage('error', 'Unauthorized access. Admin privileges required.');
            $this->redirect('home');
        }
    }
}
