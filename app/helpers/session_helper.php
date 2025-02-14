<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Flash message helper
function flash($name = '', $message = '', $class = 'bg-green-900/50 text-green-400 p-3 rounded-md text-sm mb-4') {
    if(!empty($name)) {
        if(!empty($message) && empty($_SESSION[$name])) {
            if(!empty($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }

            if(!empty($_SESSION[$name . '_class'])) {
                unset($_SESSION[$name . '_class']);
            }

            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } elseif(empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="' . $class . '">' . $_SESSION[$name] . '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

// URL redirect helper
function redirect($page) {
    header('location: ' . URLROOT . '/' . $page);
    exit;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && (!isset($_SESSION['verified']) || $_SESSION['verified']);
}

function requireVerification() {
    return isset($_SESSION['user_id']) && isset($_SESSION['verified']) && !$_SESSION['verified'];
} 