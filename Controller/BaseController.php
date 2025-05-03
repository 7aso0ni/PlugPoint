<?php
/**
 * This controller is responsible for passing main utility functions to it's children making use of their funcitonality and making the code less verbose
 */
class BaseController
{
    private function __construct()
    {
        // start the session if one is not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    /**
     * Check if user is logged in
     */
    protected function isLoggedIn()
    {
        return isset($_SESSION['user']) && isset($_COOKIE['loggedIn']) && $_COOKIE['loggedIn'] === '1';
    }

    /**
     * Redirect to specified route
     */
    protected function redirect($route)
    {
        header("Location: /PlugPoint/index.php?route=" . $route);
        exit();
    }

    /**
     * Set message in session
     */
    protected function setMessage($type, $message)
    {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
    }
}