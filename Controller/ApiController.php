<?php

namespace Controller;


use Model\UserModel;
use Model\ChargePointModel;

require_once 'Controller/BaseController.php';
require_once 'Model/UserModel.php';
require_once 'Model/ChargePointModel.php';

class ApiController extends BaseController
{
    private $chargePointModel;
    private $userModel;

    public function __construct()
    {
        // Initialize models
        $this->chargePointModel = new ChargePointModel();
        $this->userModel = new UserModel();

        // Make sure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Get nearby charging stations
     */
    public function nearbyStations()
    {
        // Get parameters
        $latitude = isset($_GET['lat']) ? (float) $_GET['lat'] : null;
        $longitude = isset($_GET['lng']) ? (float) $_GET['lng'] : null;
        $radius = isset($_GET['radius']) ? (float) $_GET['radius'] : 5;
        $exclude = isset($_GET['exclude']) ? (int) $_GET['exclude'] : null;

        // Validate required parameters
        if ($latitude === null || $longitude === null) {
            $this->outputJson(['error' => 'Missing latitude or longitude'], 400);
            return;
        }

        // Get nearby stations using our ORM method
        $stations = $this->chargePointModel->getNearbyChargePoints($latitude, $longitude, $radius);

        // Filter out the excluded station if specified
        if ($exclude !== null) {
            $stations = array_filter($stations, function ($station) use ($exclude) {
                return $station['id'] != $exclude;
            });

            // Reindex array
            $stations = array_values($stations);
        }

        // Return stations
        $this->outputJson(['stations' => $stations]);
    }

    /**
     * API endpoint for getting users with search and pagination
     */
    public function users()
    {
        // Check if user is admin
        $this->checkAdminAccess();

        // Set content type to JSON
        header('Content-Type: application/json');

        // Get pagination parameters
        $currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $perPage = 10;
        $offset = ($currentPage - 1) * $perPage;

        // Get search parameter with proper sanitization
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // Log search parameter for debugging
        error_log("API Users - Search parameter: " . $search);

        try {
            // Get users with pagination
            if (!empty($search)) {
                $users = $this->userModel->searchUsers($search, $perPage, $offset);
                $totalUsers = $this->userModel->getTotalSearchResults($search);

                // Log search results for debugging
                error_log("Search results count: " . count($users) . ", Total: " . $totalUsers);
            } else {
                $users = $this->userModel->getAllUsers($perPage, $offset);
                $totalUsers = $this->userModel->getTotalUsers();

                // Log all users for debugging
                error_log("All users count: " . count($users) . ", Total: " . $totalUsers);
            }

            $totalPages = ceil($totalUsers / $perPage);

            // Calculate pagination data
            $paginationData = [
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
                'total_items' => $totalUsers,
                'per_page' => $perPage,
                'start' => min($totalUsers, $offset + 1),
                'end' => min($totalUsers, $offset + $perPage)
            ];

            // Return JSON response
            $response = [
                'success' => true,
                'users' => $users,
                'pagination' => $paginationData
            ];

            echo json_encode($response);

        } catch (\Exception $e) {
            error_log("API Users Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while fetching users',
                'error' => $e->getMessage()
            ]);
        }

        // Exit to prevent additional output
        exit;
    }

    /**
     * Output JSON response
     */
    private function outputJson($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}