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

// Upload paths
define('UPLOAD_DIR', ROOT_PATH . 'images/services/');
define('UPLOAD_URL', BASE_URL . 'images/services/');

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

// Image upload helper function
function handleImageUpload($file, $old_image = '') {
    // Priority 1: Handle file upload (if provided)
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        // Create upload directory if it doesn't exist
        if (!file_exists(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
        
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $file['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            return ['error' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)'];
        }
        
        // Validate file size (max 5MB)
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $max_size) {
            return ['error' => 'Kích thước file không được vượt quá 5MB'];
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('service_', true) . '.' . $extension;
        $filepath = UPLOAD_DIR . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Delete old image if exists and it's a local file
            if (!empty($old_image) && !filter_var($old_image, FILTER_VALIDATE_URL)) {
                $old_filepath = ROOT_PATH . $old_image;
                if (file_exists($old_filepath) && strpos($old_filepath, UPLOAD_DIR) !== false) {
                    @unlink($old_filepath);
                }
            }
            return 'images/services/' . $filename;
        } else {
            return ['error' => 'Không thể upload file. Vui lòng thử lại.'];
        }
    }
    
    // Priority 2: If URL is provided and no file upload, use URL
    if (isset($_POST['image_url']) && !empty(trim($_POST['image_url']))) {
        $url = trim($_POST['image_url']);
        // Validate URL or treat as relative path
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        // If not a valid URL, treat as relative path
        return $url;
    }
    
    // Priority 3: If no new file and no URL, return old image
    return $old_image;
}

// Helper function to get image URL (handles both local and external URLs)
function getImageUrl($image_path) {
    if (empty($image_path)) {
        return '';
    }
    
    // If it's already a full URL (http/https), return as is
    if (filter_var($image_path, FILTER_VALIDATE_URL)) {
        return $image_path;
    }
    
    // Otherwise, treat as relative path and prepend BASE_URL
    return BASE_URL . ltrim($image_path, '/');
}
?>

