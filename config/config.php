<?php
// Main Configuration File
session_start();

// Base URL
define('BASE_URL', 'http://localhost/PTUDMNM_TL/');

// Site Information
define('SITE_NAME', 'TL Barber');
define('SITE_DESCRIPTION', 'Dịch vụ cắt tóc và làm đẹp chuyên nghiệp');

// Paths
define('ROOT_PATH', __DIR__ . '/../');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('PAGES_PATH', ROOT_PATH . 'pages/');
define('API_PATH', ROOT_PATH . 'api/');
define('ASSETS_PATH', BASE_URL . 'assets/');

// Include required files
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/jwt.php';

// Helper Functions
function redirect($url) {
    header("Location: " . $url);
    exit();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatPrice($price) {
    return number_format($price, 0, ',', '.') . ' đ';
}

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}
?>

