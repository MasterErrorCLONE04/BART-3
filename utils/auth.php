<?php
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit();
    }
}

function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] != $role) {
        header('Location: ../unauthorized.php');
        exit();
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'],
            'full_name' => $_SESSION['full_name']
        ];
    }
    return null;
}
?>
