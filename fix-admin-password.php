<?php
/**
 * Fix Admin Password
 * Run this once to update admin password to 123456
 */

require_once __DIR__ . '/config/config.php';

$pdo = getDBConnection();

// Hash for password: 123456
$new_password = password_hash('123456', PASSWORD_DEFAULT);

// Update admin password
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@tlbarber.com'");
if ($stmt->execute([$new_password])) {
    echo "✅ Admin password updated successfully!\n";
    echo "New password: 123456\n";
} else {
    echo "❌ Error updating password\n";
}
?>

