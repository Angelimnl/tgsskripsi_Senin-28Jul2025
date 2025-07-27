<?php
session_start();

function isPemilik() {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2; // Pemilik
}

function isAdmin() {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1; // Admin
}

function isUser() {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == 5; // User
}

function allowRoles($roles = []) {
    if (!in_array($_SESSION['role_id'], $roles)) {
        echo "<script>alert('Anda tidak memiliki akses ke halaman ini.');window.location='design.php';</script>";
        exit();
    }
}
?>

