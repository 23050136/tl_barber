<?php
/**
 * TL Barber Installation Script
 * Run this file once to set up the database
 */

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'tl_barber';

echo "<h2>TL Barber - Database Installation</h2>";
echo "<pre>";

try {
    // Connect to MySQL server (without database)
    $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to MySQL server\n";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '$db_name' created or already exists\n";
    
    // Select database
    $pdo->exec("USE `$db_name`");
    echo "✓ Using database '$db_name'\n";
    
    // Read and execute SQL file
    $sql_file = __DIR__ . '/database/schema.sql';
    if (!file_exists($sql_file)) {
        die("✗ SQL file not found: $sql_file\n");
    }
    
    $sql = file_get_contents($sql_file);
    
    // Remove CREATE DATABASE and USE statements (already handled)
    $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/USE.*?;/i', '', $sql);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $executed = 0;
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                $pdo->exec($statement);
                $executed++;
            } catch (PDOException $e) {
                // Ignore errors for existing tables/constraints
                if (strpos($e->getMessage(), 'already exists') === false && 
                    strpos($e->getMessage(), 'Duplicate') === false) {
                    echo "⚠ Warning: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "✓ Executed $executed SQL statements\n";
<<<<<<< HEAD
=======
    
    // Add new columns for advanced features
    echo "\nAdding advanced features columns...\n";
    $columns_to_add = [
        'payment_status' => "ENUM('pending', 'paid', 'refunded') DEFAULT 'pending'",
        'payment_method' => "VARCHAR(50) DEFAULT NULL",
        'payment_transaction_id' => "VARCHAR(100) DEFAULT NULL",
        'qr_code' => "VARCHAR(255) DEFAULT NULL",
        'check_in_time' => "DATETIME DEFAULT NULL"
    ];
    
    foreach ($columns_to_add as $column_name => $column_def) {
        // Check if column exists
        try {
            $check = $pdo->query("SHOW COLUMNS FROM bookings LIKE '$column_name'");
            if ($check->rowCount() > 0) {
                echo "  ⚠ Column '$column_name' already exists, skipping...\n";
                continue;
            }
        } catch (PDOException $e) {
            // Table might not exist yet, continue
        }
        
        // Add column
        try {
            $pdo->exec("ALTER TABLE bookings ADD COLUMN $column_name $column_def");
            echo "  ✓ Added column: $column_name\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false || 
                strpos($e->getMessage(), 'already exists') !== false) {
                echo "  ⚠ Column '$column_name' already exists\n";
            } else {
                echo "  ⚠ Warning: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Create auto_confirmation_settings table
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS auto_confirmation_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            is_enabled BOOLEAN DEFAULT FALSE,
            auto_confirm_hours INT DEFAULT 24 COMMENT 'Auto confirm bookings X hours before',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // Insert default if not exists
        $check = $pdo->query("SELECT COUNT(*) as count FROM auto_confirmation_settings");
        if ($check->fetch()['count'] == 0) {
            $pdo->exec("INSERT INTO auto_confirmation_settings (is_enabled, auto_confirm_hours) VALUES (FALSE, 24)");
        }
        echo "  ✓ Created auto_confirmation_settings table\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') === false) {
            echo "  ⚠ Warning: " . $e->getMessage() . "\n";
        }
    }
    
    // Update admin password to 123456
    echo "\nUpdating admin password...\n";
    $admin_password = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@tlbarber.com'");
    if ($stmt->execute([$admin_password])) {
        echo "  ✓ Admin password updated to: 123456\n";
    } else {
        echo "  ⚠ Could not update admin password\n";
    }
    
    // Update barber passwords to 123456
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE role = 'barber'");
    if ($stmt->execute([$admin_password])) {
        echo "  ✓ Barber passwords updated to: 123456\n";
    }
    
>>>>>>> e906b55 (update code)
    echo "\n";
    echo "✅ Installation completed successfully!\n";
    echo "\n";
    echo "Default accounts:\n";
    echo "  Admin: admin@tlbarber.com / 123456\n";
    echo "  Barber: barber1@tlbarber.com / 123456\n";
    echo "\n";
    echo "⚠ Please delete this file (install.php) after installation for security.\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "\n";
    echo "Please check your database configuration in config/database.php\n";
}

echo "</pre>";
?>

