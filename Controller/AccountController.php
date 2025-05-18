<?php

namespace Controller;

use \Model\UserModel;

class AccountController extends BaseController
{
    private $userModel = null;

    public function __construct()
    {
        $this->userModel = new UserModel();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function showAccountPage(): void
    {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            header('Location: index.php?route=login');
            exit();
        }

        $title = "My Account";
        $user = $_SESSION['user']; // Use session data

        ob_start();
        require 'View/account/main.php';
        $content = ob_get_clean();

        require 'View/layouts/main.php';
    }

    public function updateUser()
    {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            header('Location: index.php?route=login');
            exit();
        }

        $title = "My Account";
        $user = $_SESSION['user']; // Get current user data
        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get current user email
            $currentEmail = $user['email'];

            // Collect inputs
            $first_name = trim($_POST['first_name'] ?? "");
            $last_name = trim($_POST['last_name'] ?? "");
            $phone = trim($_POST['phone'] ?? "");

            // Validate inputs
            if (empty($first_name) || empty($last_name)) {
                $error = "First name and last name are required.";
            } else {
                // Prepare data for update
                $updatedUser = [
                    'name' => $first_name . " " . $last_name,
                    'phone' => $phone
                ];

                // Update user in database
                $result = $this->userModel->updateUserData($updatedUser, $currentEmail);

                if ($result) {
                    // If update successful, update session data
                    $_SESSION['user']['name'] = $updatedUser['name'];
                    $_SESSION['user']['phone'] = $updatedUser['phone'];

                    // Set success message
                    $this->setMessage('success', 'Your profile has been updated successfully.');

                    // Redirect to avoid form resubmission
                    header('Location: index.php?route=account');
                    exit();
                } else {
                    $error = "Failed to update profile. Please try again.";
                }
            }
        }

        // Render the page
        ob_start();
        require 'View/account/main.php';
        $content = ob_get_clean();

        require 'View/layouts/main.php';
    }
}