<?php
/**
 * Quick script to add reply columns to reviews table
 * Run this once: http://localhost/PTUDMNM_TL/add_reply_columns.php
 */

require_once __DIR__ . '/config/config.php';

$pdo = getDBConnection();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Reply Columns</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .success { color: green; padding: 10px; background: #d4edda; border-radius: 4px; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #f8d7da; border-radius: 4px; margin: 10px 0; }
        .info { color: blue; padding: 10px; background: #d1ecf1; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Thêm cột Reply vào bảng Reviews</h2>
        <pre>
<?php
try {
    echo "Đang kiểm tra bảng reviews...\n\n";
    
    // Check if reply column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM reviews LIKE 'reply'");
    $reply_exists = $stmt->rowCount() > 0;
    
    // Check if reply_at column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM reviews LIKE 'reply_at'");
    $reply_at_exists = $stmt->rowCount() > 0;
    
    if ($reply_exists && $reply_at_exists) {
        echo "✓ Cột 'reply' đã tồn tại\n";
        echo "✓ Cột 'reply_at' đã tồn tại\n";
        echo "\n✅ Database đã được cập nhật! Bạn có thể xóa file này.\n";
    } else {
        echo "Đang thêm các cột mới...\n\n";
        
        // Add reply column
        if (!$reply_exists) {
            try {
                $pdo->exec("ALTER TABLE reviews ADD COLUMN reply TEXT DEFAULT NULL");
                echo "✓ Đã thêm cột 'reply'\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                    echo "✓ Cột 'reply' đã tồn tại\n";
                } else {
                    throw $e;
                }
            }
        } else {
            echo "✓ Cột 'reply' đã tồn tại\n";
        }
        
        // Add reply_at column
        if (!$reply_at_exists) {
            try {
                $pdo->exec("ALTER TABLE reviews ADD COLUMN reply_at TIMESTAMP NULL DEFAULT NULL");
                echo "✓ Đã thêm cột 'reply_at'\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                    echo "✓ Cột 'reply_at' đã tồn tại\n";
                } else {
                    throw $e;
                }
            }
        } else {
            echo "✓ Cột 'reply_at' đã tồn tại\n";
        }
        
        echo "\n✅ Hoàn thành! Database đã được cập nhật thành công.\n";
        echo "Bạn có thể xóa file add_reply_columns.php này.\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Lỗi: " . $e->getMessage() . "\n";
    echo "\nVui lòng chạy SQL sau trong phpMyAdmin hoặc MySQL:\n\n";
    echo "ALTER TABLE reviews ADD COLUMN reply TEXT DEFAULT NULL;\n";
    echo "ALTER TABLE reviews ADD COLUMN reply_at TIMESTAMP NULL DEFAULT NULL;\n";
}
?>
        </pre>
        <div style="margin-top: 20px;">
            <a href="<?php echo BASE_URL; ?>pages/booking-history.php" style="padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">
                Quay lại Lịch sử đặt lịch
            </a>
        </div>
    </div>
</body>
</html>

