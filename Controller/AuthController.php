<?php

namespace Controller;

use \Exception;
use \Model\UserModel;

class AuthController
{
    private $userModel =  null;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function SignUp()
    {
        $title = "Sign Up";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // check if all any of the fields are empty and show the error
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

            $user = [
                'name' => $first_name . ' ' . $last_name,
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'role_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // create the user object
            $this->userModel->createUser($user);

            // start the session if all checks pass
            session_start();

            $_SESSION['user'] = $user;
            $_SESSION['loggedIn'] = true;

            // if successful redirect to home
            header("Location: /MVCProject/index.php");
        }

        ob_start();
        require 'View/auth/signup/main.php';
        $content = ob_get_clean();

        require 'View/layouts/auth.php';
    }

    public function Login()
    {
        $title = "Login";

        if  ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = "All fields are required";
            }

            if (!$this->userModel->doesUserExist($email) && empty($error)) {
                $error = "User does not exists";
            }

            $user = $this->userModel->getUserByEmail($email);
            if  ($user->password !== $password && empty($error)) {
                $error = "email or password is incorrect";
            }

            session_start();
            $_SESSION['user'] = $user;
            $_SESSION['loggedIn'] = true;

            header("Location: /MVCProject/index.php");
        }

        ob_start();
        require 'View/auth/login/main.php';
        $content = ob_get_clean();

        require 'View/layouts/auth.php';
    }

    public function Logout() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                session_destroy();
                header("Location: /MVCProject/index.php");
            }
        }
    }
}