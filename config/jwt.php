<?php
// JWT Configuration
define('JWT_SECRET', 'tl_barber_secret_key_2024_very_secure');
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRATION', 86400); // 24 hours

// Try to load JWT library, fallback to session if not available
$jwt_available = false;
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    if (class_exists('Firebase\JWT\JWT')) {
        $jwt_available = true;
    }
}

function generateJWT($user) {
    global $jwt_available;
    
    if ($jwt_available) {
        $payload = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + JWT_EXPIRATION
        ];
        return \Firebase\JWT\JWT::encode($payload, JWT_SECRET, JWT_ALGORITHM);
    } else {
        // Fallback to session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        return 'session_token';
    }
}

function verifyJWT($token) {
    global $jwt_available;
    
    if ($jwt_available) {
        try {
            $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key(JWT_SECRET, JWT_ALGORITHM));
            return (array) $decoded;
        } catch (Exception $e) {
            return null;
        }
    } else {
        // Fallback to session
        if (isset($_SESSION['user_id'])) {
            return [
                'user_id' => $_SESSION['user_id'],
                'email' => $_SESSION['user_email'],
                'role' => $_SESSION['user_role']
            ];
        }
        return null;
    }
}

function getCurrentUser() {
    global $jwt_available;
    
    if ($jwt_available && isset($_COOKIE['auth_token'])) {
        $token = $_COOKIE['auth_token'];
        $decoded = verifyJWT($token);
    } else {
        $decoded = verifyJWT(null);
    }
    
    if (!$decoded) {
        return null;
    }
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$decoded['user_id']]);
    return $stmt->fetch();
}
?>

