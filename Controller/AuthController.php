<?php

namespace Controller;

use \Exception;
use \Model\UserModel;

class AuthController
{
    private $userModel = null;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function SignUp()
    {
        $title = "Sign Up";
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // check if any of the fields are empty and show the error
            if (empty(trim($first_name)) || empty(trim($last_name)) || empty(trim($email)) || empty(trim($phone)) || empty(trim($password)) || empty(trim($confirm_password))) {
                $error = "All fields are required";
            }

            // check if both passwords are similar and there wasn't an error before, then display the error message
            if ($password != $confirm_password && empty($error)) {
                $error = "Passwords do not match";
            }

            if ($this->userModel->doesUserExist($email) && empty($error)) {
                $error = "User already exists";
            }

            // Only create user if there are no errors
            if (empty($error)) {
                $user = [
                    'name' => $first_name . ' ' . $last_name,
                    'email' => $email,
                    'phone' => $phone,
                    'password' => $password, // Password will be hashed in UserModel::createUser
                    'role_id' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                // create the user object (with hashed password)
                $this->userModel->createUser($user);

                // Get the user with all DB fields after creation
                $createdUser = $this->userModel->getUserByEmail($email);

                // Remove password from session data
                unset($createdUser['password']);

                // start the session if all checks pass
                session_start();

                $_SESSION['user'] = $createdUser;
                $_SESSION['loggedIn'] = true;

                setcookie("loggedIn", true, time() + (86400 * 30), "/");

                // if successful redirect to home
                header("Location: /PlugPoint/index.php?route=home");
                exit(); // Added exit after redirect
            }
        }

        ob_start();
        require 'View/auth/signup/main.php';
        $content = ob_get_clean();

        require 'View/layouts/main.php';
    }

    public function Login()
    {
        $title = "Login";
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = "All fields are required";
            }

            if (!$this->userModel->doesUserExist($email) && empty($error)) {
                $error = "User does not exist";
            }

            if (empty($error)) {
                $user = $this->userModel->getUserByEmail($email);

                // Use password_verify to check if entered password matches stored hash
                if (!$this->userModel->verifyPassword($password, $user['password'])) {
                    $error = "Email or password is incorrect";
                } else {
                    // Only start session and log in if password is correct
                    session_start();
                    $first_name = explode(' ', $user['name'])[0];
                    $last_name = explode(' ', $user['name'])[1];

                    setcookie("loggedIn", true, time() + (86400 * 30), "/");

                    // Remove password from session data for security
                    unset($user['password']);
                    $_SESSION['user'] = $user;

                    header("Location: /PlugPoint/index.php?route=home");
                    exit(); // Added exit after redirect
                }
            }
        }

        ob_start();
        require 'View/auth/login/main.php';
        $content = ob_get_clean();

        require 'View/layouts/main.php';
    }

    public function Logout()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Clear all session data
            session_start();
            session_unset();
            session_destroy();

            // Clear cookies
            setcookie("first_name", "", time() - 3600, "/");
            setcookie("last_name", "", time() - 3600, "/");
            setcookie("loggedIn", "", time() - 3600, "/");
            setcookie("email", "", time() - 3600, "/");
            setcookie("phone", "", time() - 3600, "/");

            header("Location: /PlugPoint/index.php?route=login");
            exit(); // Added exit after redirect
        }
    }
}