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

    public function updateUser()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            $currentEmail = $_COOKIE["email"] ?? null;

            if (!$currentEmail) {
                $error = "Not logged in.";
            }

            // Collect raw inputs
            $first_name = $_POST['first_name'] ?? "";
            $last_name = $_POST['last_name'] ?? "";
            $phone = $_POST['phone'] ?? "";

            $updatedUser = [];

            // Only include fields that are non-empty
            if (!empty($name)) {
                $updatedUser['name'] = $first_name . " " . $last_name;
            }

            if (!empty($phone)) {
                $updatedUser['phone'] = $phone;
            }

            if (empty($updatedUser)) {
                $error = "Please provide at least one field to update.";
            }


            // Call updateUserData with the correct parameter order (userData, currentEmail)
            $result = $this->userModel->updateUserData($updatedUser, $currentEmail);

            // Update cookies with new values if update was successful

        }


        // render update page
        ob_start();
        require 'View/account/main.php';
        $content = ob_get_clean();

        require 'View/layouts/auth.php';
    }

}