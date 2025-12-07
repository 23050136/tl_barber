<?php
// Admin Authentication Check
require_once __DIR__ . '/../config/config.php';

function checkAdminAuth() {
    $user = getCurrentUser();
    
    if (!$user) {
        redirect(BASE_URL . 'pages/login.php');
    }
    
    if ($user['role'] !== 'admin') {
        redirect(BASE_URL . 'index.php');
    }
    
    return $user;
}
?>

