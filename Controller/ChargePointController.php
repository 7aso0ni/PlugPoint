<?php

namespace Controller;

require_once 'Model/ChargePointModel.php';
require_once 'Model/UserModel.php';
require_once 'Model/BookingModel.php';

require_once 'BaseController.php';


use BaseController;
use Model\ChargePointModel;
use Model\UserModel;
use Model\BookingModel;

class ChargePointController extends BaseController
{
    private ChargePointModel $chargePointModel;
    private UserModel $userModel;
    private BookingModel $bookingModel;

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
    public function index()
    {
        $title = 'EV Charging Stations';

        $perPage = 8;

        // Get all charge points (both available and unavailable) for initial page load
        // Use getFilteredChargePoints with null availability to show all
        $chargePoints = $this->chargePointModel->getFilteredChargePoints(
            '', // No search term
            1.00, // High max price to show all
            null, // Null means show both available and unavailable
            $perPage,
            0 // First page
        );

        $totalCount = $this->chargePointModel->getFilteredChargePointsCount(
            '', // No search term
            1.00, // High max price to show all
            null // Null means show both available and unavailable
        );

        $totalPages = (int) ceil($totalCount / $perPage);

        $initialData = [
            'chargePoints' => $chargePoints,
            'pagination' => [
                'current_page' => 1,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_count' => $totalCount,
            ],
        ];

        /* view */
        ob_start();
        require 'View/shop/main.php';
        $content = ob_get_clean();

        /* layout */
        require 'View/layouts/main.php';
    }

    /* ─────────────────────────────────────────────────────────── */
    /* AJAX FILTER END-POINT                                      */
    /* ─────────────────────────────────────────────────────────── */
    public function filter()
    {
        $search = $_POST['search'] ?? '';
        $maxPrice = (float) ($_POST['max_price'] ?? 0.50);
        $available = $_POST['available'] !== '' ? (int) $_POST['available'] : null;
        $page = (int) ($_POST['page'] ?? 1);
        $perPage = 8;
        $offset = ($page - 1) * $perPage;

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
}