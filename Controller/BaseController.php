<?php
namespace Controller;

/**
 * This controller is responsible for passing main utility functions to it's children making use of their funcitonality and making the code less verbose
 */
class BaseController
{
    protected function __construct()
    {
        // start the session if one is not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check if the current user has admin access
     */
    protected function checkAdminAccess()
    {
        if (!isset($_SESSION['user']) || (int) ($_SESSION['user']['role_id'] ?? 0) !== 1) {
            $this->setMessage('error', 'Access denied. You do not have admin privileges.');
            $this->redirect('login');
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
        header("Location: index.php?route=" . $route);
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