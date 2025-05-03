<?php

namespace Controller;

use BaseController;
use Model\ChargePointModel;

require_once 'BaseController.php';

class ApiController extends BaseController
{
    private $chargePointModel;

    public function __construct()
    {
        $this->chargePointModel = new ChargePointModel();
    }

    /**
     * Get nearby charging stations
     * 
     * This endpoint returns charging stations near a given location
     * 
     * URL parameters:
     * - lat: Latitude (required)
     * - lng: Longitude (required)
     * - radius: Search radius in kilometers (optional, default 5)
     * - exclude: ID of station to exclude (optional)
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