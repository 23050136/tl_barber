<?php
/**
 * Update Database - Add missing columns
 * Run this if you get errors about missing columns
 */

require_once __DIR__ . '/config/config.php';

$pdo = getDBConnection();

echo "<h2>TL Barber - Database Update</h2>";
echo "<pre>";

try {
    echo "Adding missing columns to bookings table...\n\n";
    
    // Check and add columns
    $columns = [
        'payment_status' => "ALTER TABLE bookings ADD COLUMN payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending'",
        'payment_method' => "ALTER TABLE bookings ADD COLUMN payment_method VARCHAR(50) DEFAULT NULL",
        'payment_transaction_id' => "ALTER TABLE bookings ADD COLUMN payment_transaction_id VARCHAR(100) DEFAULT NULL",
        'qr_code' => "ALTER TABLE bookings ADD COLUMN qr_code VARCHAR(255) DEFAULT NULL",
        'check_in_time' => "ALTER TABLE bookings ADD COLUMN check_in_time DATETIME DEFAULT NULL"
    ];
    
    foreach ($columns as $column_name => $sql) {
        // Check if column exists
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM bookings LIKE '$column_name'");
            if ($stmt->rowCount() > 0) {
                echo "✓ Column '$column_name' already exists\n";
            } else {
                $pdo->exec($sql);
                echo "✓ Added column: $column_name\n";
            }
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column') === false) {
                echo "⚠ Error with $column_name: " . $e->getMessage() . "\n";
            } else {
                echo "✓ Column '$column_name' already exists\n";
            }
        }
    }
    
    // Create auto_confirmation_settings table
    echo "\nChecking auto_confirmation_settings table...\n";
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS auto_confirmation_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            is_enabled BOOLEAN DEFAULT FALSE,
            auto_confirm_hours INT DEFAULT 24 COMMENT 'Auto confirm bookings X hours before',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        $check = $pdo->query("SELECT COUNT(*) as count FROM auto_confirmation_settings");
        if ($check->fetch()['count'] == 0) {
            $pdo->exec("INSERT INTO auto_confirmation_settings (is_enabled, auto_confirm_hours) VALUES (FALSE, 24)");
        }
        echo "✓ auto_confirmation_settings table ready\n";
    } catch (PDOException $e) {
        echo "⚠ " . $e->getMessage() . "\n";
    }
    
    // Update admin password
    echo "\nUpdating admin password...\n";
    $admin_password = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@tlbarber.com'");
    if ($stmt->execute([$admin_password])) {
        echo "✓ Admin password updated to: 123456\n";
    }
    
    echo "\n✅ Database update completed!\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>

