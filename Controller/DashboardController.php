<?php

namespace Controller;

require_once 'Model/BookingModel.php';

use Model\BookingModel;

class DashboardController
{
    private $bookingModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
    }

    public function index()
    {
        session_start();
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            header('Location: /index.php?route=login');
            exit();
        }

        $rentals = $this->bookingModel->getUserBookings($userId);

        $title = "Dashboard";

        ob_start();
        require 'View/dashboard/main.php';
        $content = ob_get_clean();

        require 'View/layouts/main.php';
    }

    public function cancelRental()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['booking_id'] ?? null;

            if ($bookingId) {
                $this->bookingModel->cancelBooking($bookingId);
            }

            header('Location: /index.php?route=dashboard');
            exit();
        }
    }
}
