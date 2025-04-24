<?php

namespace Controller;

use \Model\UserModel;

class AccountController
{
    private $userModel = null;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function showAccountPage()
    {
        // Check if user is logged in
        if (empty($_COOKIE['loggedIn']) || $_COOKIE['loggedIn'] !== "1") {
            header('Location: index.php?route=login');
            exit();
        }

        $title = "My Account";

        ob_start();
        require 'View/account/main.php';
        $content = ob_get_clean();

        require 'View/layouts/main.php';
    }

    public function updateProfile()
    {
        // Check if user is logged in
        if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
            header('Location: index.php?route=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';
            $city = $_POST['city'] ?? '';
            $state = $_POST['state'] ?? '';
            $zip = $_POST['zip'] ?? '';

            // Basic validation
            if (empty(trim($first_name)) || empty(trim($last_name)) || empty(trim($email)) || empty(trim($phone))) {
                $_SESSION['error'] = "Required fields cannot be empty";
                header('Location: index.php?route=account');
                exit();
            }

            // Check if email is being changed and if it already exists for another user
            if ($email !== $_SESSION['user']['email'] && $this->userModel->doesUserExist($email)) {
                $_SESSION['error'] = "Email address is already in use";
                header('Location: index.php?route=account');
                exit();
            }

            // Update user data
            $user = [
                'id' => $_SESSION['user']['id'],
                'name' => $first_name . ' ' . $last_name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'zip' => $zip
            ];

            // Update user in database
            $result = $this->userModel->updateUser($user);

            if ($result) {
                // Update session data
                $_SESSION['user'] = array_merge($_SESSION['user'], $user);
                $_SESSION['message'] = "Profile updated successfully";
            } else {
                $_SESSION['error'] = "Failed to update profile";
            }

            header('Location: index.php?route=account');
            exit();
        }
    }

    public function updatePassword()
    {
        // Check if user is logged in
        if (empty($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
            header('Location: index.php?route=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Get current user data
            $user = $this->userModel->getUserById($_SESSION['user']['id']);

            // Verify current password
            if ($user->password !== $current_password) {
                $_SESSION['error'] = "Current password is incorrect";
                header('Location: index.php?route=account#security');
                exit();
            }

            // Check if new passwords match
            if ($new_password !== $confirm_password) {
                $_SESSION['error'] = "New passwords do not match";
                header('Location: index.php?route=account#security');
                exit();
            }

            // Validate password strength (basic example)
            if (strlen($new_password) < 8) {
                $_SESSION['error'] = "Password must be at least 8 characters long";
                header('Location: index.php?route=account#security');
                exit();
            }

            // Update password in database
            $result = $this->userModel->updatePassword($_SESSION['user']['id'], $new_password);

            if ($result) {
                $_SESSION['message'] = "Password updated successfully";
            } else {
                $_SESSION['error'] = "Failed to update password";
            }

            header('Location: index.php?route=account#security');
            exit();
        }
    }
}