<?php

namespace Controller;


use Model\BookingModel;
use Model\ChargePointModel;
use Model\UserModel;

require_once 'Controller/BaseController.php';


class BookingController extends BaseController
{
    private $bookingModel;
    private $chargePointModel;
    private $userModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->chargePointModel = new ChargePointModel();
        $this->userModel = new UserModel();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }


    /**
     * Display booking form for a charge point
     */
    public function BookingForm()
    {
        $title = "Book Charging Station";
        $error = null;

        // Ensure user is logged in
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }

        // Get charge point ID from URL
        $cpId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$cpId) {
            $this->redirect('chargepoints');
            return;
        }

        // Get charge point details
        $cp = $this->chargePointModel->getChargePointById($cpId);
        if (!$cp) {
            $error = "Charge point not found";
            $this->setMessage('error', $error);
            $this->redirect('chargepoints');
            return;
        }

        // Check if charge point is available
        if (!$cp['availability']) {
            $error = "This charging station is currently unavailable";
            $this->setMessage('error', $error);
            $this->redirect('chargepoints/details&id=' . $cpId);
            return;
        }

        // Check if the user is the owner of the charge point
        if ($cp['owner_id'] == $_SESSION['user']['id']) {
            $error = "You cannot book your own charging station";
            $this->setMessage('error', $error);
            $this->redirect('chargepoints/details&id=' . $cpId);
            return;
        }

        // Get owner details
        $owner = $this->userModel->getUserById($cp['owner_id']);
        if ($owner) {
            $cp['owner_name'] = $owner['name'];
            $cp['owner_email'] = $owner['email'];
        }

        // Get booked slots for calendar visualization (next 30 days)
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+30 days'));
        $bookedSlots = $this->bookingModel->getBookedSlots($cpId, $startDate, $endDate);

        // Render view
        ob_start();
        require 'View/booking/main.php';
        $content = ob_get_clean();

        require 'View/layouts/main.php';
    }

    /**
     * Process booking request
     */
    public function CreateBooking()
    {
        /* user must be logged in */
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }

        /* ---------- grab & validate raw POST --------------------- */
        $cpId = (int) ($_POST['charge_point_id'] ?? 0);
        $date = $_POST['booking_date'] ?? '';   // Y-m-d
        $time = $_POST['start_time'] ?? '';   // HH:MM
        $duration = (float) ($_POST['duration'] ?? 0);  // hours

        if (!$cpId || !$date || !$time || $duration <= 0) {
            $this->setMessage('error', 'All fields are required');
            $this->redirect('booking&id=' . $cpId);
            return;
        }

        /* ---------- construct DateTime objects ------------------- */
        try {
            $bookingDate = new \DateTime("$date $time:00");  // local server TZ
        } catch (\Exception $e) {
            $this->setMessage('error', 'Invalid date/time');
            $this->redirect('booking&id=' . $cpId);
            return;
        }

        $dueDate = clone $bookingDate;
        $dueDate->modify('+' . $duration . ' hours');

        /* ---------- time sanity checks --------------------------- */
        $now = new \DateTime();
        if ($bookingDate < $now) {
            $this->setMessage('error', 'Booking date cannot be in the past');
            $this->redirect('booking&id=' . $cpId);
            return;
        }
        if ($dueDate <= $bookingDate) {
            $this->setMessage('error', 'End date must be after start date');
            $this->redirect('booking&id=' . $cpId);
            return;
        }

        /* ---------- price calculation ---------------------------- */
        $cp = $this->chargePointModel->getChargePointById($cpId);
        if (!$cp) {
            $this->setMessage('error', 'Charge point not found');
            $this->redirect('chargepoints');
            return;
        }

        $hours = $duration;
        $estimatedKwh = $hours * 7;                        // 7 kW charger
        $estimatedPrice = $estimatedKwh * $cp['price_per_kWh'];

        $bookingData = [
            'user_id' => $_SESSION['user']['id'],
            'charge_point_id' => $cpId,
            'booking_date' => $bookingDate->format('Y-m-d H:i:s'),
            'due_date' => $dueDate->format('Y-m-d H:i:s'),
            'status' => 'Pending',
            'estimated_kwh' => $estimatedKwh,
            'estimated_price' => $estimatedPrice,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        /* ---------- reserve (conflict check inside model) -------- */
        try {
            $booking = $this->bookingModel->reserve($bookingData);

            $this->setMessage(
                'success',
                'Booking request submitted! Your booking is pending approval.'
            );
            $this->redirect('booking_confirmation&id=' . $booking['id']);
        } catch (\Exception $e) {
            $this->setMessage('error', $e->getMessage());
            $this->redirect('booking&id=' . $cpId);
        }
    }


    /**
     * Display booking confirmation
     */
    public function BookingConfirmation()
    {
        $title = "Booking Confirmation";

        // Ensure user is logged in
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }

        // Get booking ID from URL
        $bookingId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$bookingId) {
            $this->redirect('dashboard');
            return;
        }

        // Get booking details
        $booking = $this->bookingModel->getBookingById($bookingId);
        if (!$booking || $booking['user_id'] != $_SESSION['user']['id']) {
            $this->setMessage('error', 'Booking not found');
            $this->redirect('dashboard');
            return;
        }

        // Get charge point details
        $cp = $this->chargePointModel->getChargePointById($booking['charge_point_id']);
        if (!$cp) {
            $this->setMessage('error', 'Charge point not found');
            $this->redirect('dashboard');
            return;
        }

        // Get owner details
        $owner = $this->userModel->getUserById($cp['owner_id']);
        if ($owner) {
            $cp['owner_name'] = $owner['name'];
            $cp['owner_email'] = $owner['email'];
        }

        // Render view
        ob_start();
        require 'View/booking/booking_confirmation.php';
        $content = ob_get_clean();

        require 'View/layouts/main.php';
    }

    /**
     * Cancel a booking (user action)
     */
    public function CancelBooking(): void
    {
        /* ── 1 Auth ──────────────────────────────────────────────── */
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }

        /* ── 2 Fetch ID from POST first, fallback to GET ─────────── */
        $bookingId = 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = (int) ($_POST['booking_id'] ?? 0);
        }
        if (!$bookingId) {                         // not POST? try query-string
            $bookingId = (int) ($_GET['id'] ?? 0);
        }
        if (!$bookingId) {
            $this->setMessage('error', 'Missing booking ID');
            $this->redirect('dashboard');
            return;
        }

        /* ── 3 Ownership / status checks (unchanged) ─────────────── */
        $booking = $this->bookingModel->getBookingById($bookingId);
        if (!$booking || $booking['user_id'] !== $_SESSION['user']['id']) {
            $this->setMessage('error', 'Booking not found or access denied');
            $this->redirect('dashboard');
            return;
        }

        $now = new \DateTime();
        $bookingDate = new \DateTime($booking['booking_date']);

        if ($bookingDate < $now) {
            $this->setMessage('error', 'Cannot cancel a booking that has already started');
            $this->redirect('dashboard');
            return;
        }

        if ($booking['status'] === 'Canceled') {
            $this->setMessage('error', 'Booking is already canceled');
            $this->redirect('dashboard');
            return;
        }

        /* ── 4 Cancel row and check result ───────────────────────── */
        $rows = $this->bookingModel->cancelBooking($bookingId);

        if ($rows) {
            $this->setMessage('success', 'Booking canceled successfully');
        } else {
            $this->setMessage('error', 'Unable to cancel booking. Please try again.');
        }

        $this->redirect('dashboard');
    }



    /**
     * Get available time slots for a charge point (AJAX)
     */
    public function GetAvailableSlots()
    {
        // Ensure request is AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            return;
        }

        // Get parameters
        $cpId = isset($_GET['cp_id']) ? (int) $_GET['cp_id'] : 0;
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

        if (!$cpId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing charge point ID']);
            return;
        }

        // Get booked slots for the date
        $bookedSlots = $this->bookingModel->getBookedSlots($cpId, $date, $date);

        // Convert to time ranges
        $unavailableTimes = [];
        foreach ($bookedSlots as $slot) {
            $start = new \DateTime($slot['booking_date']);
            $end = new \DateTime($slot['due_date']);

            $unavailableTimes[] = [
                'start' => $start->format('H:i'),
                'end' => $end->format('H:i'),
                'status' => $slot['status']
            ];
        }

        // Return available slots
        echo json_encode([
            'date' => $date,
            'unavailable_slots' => $unavailableTimes
        ]);
    }

    /**
     * Show user's bookings
     */
    public function MyBookings()
    {

        $title = "My Bookings";

        // Ensure user is logged in
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }

        // Get user's bookings
        $rentals = $this->bookingModel->getBookingsByUser($_SESSION['user']['id']);

        // Render view
        ob_start();
        require 'View/dashboard/main.php';
        $content = ob_get_clean();

        require 'View/layouts/main.php';
    }

    /**
     * Approve a booking request (for charger owners)
     */
    public function ApproveBooking()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $bookingId = $_POST['booking_id'] ?? null;

        if (!$bookingId) {
            $this->setMessage('error', 'Missing booking ID');
            $this->redirect('homeowner/my_chargers');
            return;
        }

        // Get the booking details
        $booking = $this->bookingModel->getBookingById($bookingId);
        if (!$booking) {
            $this->setMessage('error', 'Booking not found');
            $this->redirect('homeowner/my_chargers');
            return;
        }

        // Get the charge point to verify ownership
        $chargePoint = $this->chargePointModel->getChargePointById($booking['charge_point_id']);
        if (!$chargePoint || $chargePoint['owner_id'] != $userId) {
            $this->setMessage('error', 'You do not have permission to approve this booking');
            $this->redirect('homeowner/my_chargers');
            return;
        }

        // Verify booking is in Pending status
        if ($booking['status'] !== 'Pending') {
            $this->setMessage('error', 'This booking is no longer pending');
            $this->redirect('homeowner/my_chargers');
            return;
        }

        // Update booking status to Approved
        $result = $this->bookingModel->updateBookingStatus($bookingId, 'Approved');

        if ($result) {
            $this->setMessage('success', 'Booking request approved successfully');
        } else {
            $this->setMessage('error', 'Failed to approve booking. Please try again.');
        }

        $this->redirect('homeowner/my_chargers');
    }

    /**
     * Decline a booking request (for charger owners)
     */
    public function DeclineBooking()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $bookingId = $_POST['booking_id'] ?? null;

        if (!$bookingId) {
            $this->setMessage('error', 'Missing booking ID');
            $this->redirect('homeowner/my_chargers');
            return;
        }

        // Get the booking details
        $booking = $this->bookingModel->getBookingById($bookingId);
        if (!$booking) {
            $this->setMessage('error', 'Booking not found');
            $this->redirect('homeowner/my_chargers');
            return;
        }

        // Get the charge point to verify ownership
        $chargePoint = $this->chargePointModel->getChargePointById($booking['charge_point_id']);
        if (!$chargePoint || $chargePoint['owner_id'] != $userId) {
            $this->setMessage('error', 'You do not have permission to decline this booking');
            $this->redirect('homeowner/my_chargers');
            return;
        }

        // Verify booking is in Pending status
        if ($booking['status'] !== 'Pending') {
            $this->setMessage('error', 'This booking is no longer pending');
            $this->redirect('homeowner/my_chargers');
            return;
        }

        // Update booking status to Declined
        $result = $this->bookingModel->updateBookingStatus($bookingId, 'Declined');

        if ($result) {
            $this->setMessage('success', 'Booking request declined successfully');
        } else {
            $this->setMessage('error', 'Failed to decline booking. Please try again.');
        }

        $this->redirect('homeowner/my_chargers');
    }
}