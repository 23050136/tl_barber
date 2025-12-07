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

