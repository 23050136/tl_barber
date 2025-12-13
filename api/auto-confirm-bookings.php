<?php
/**
 * Auto Confirm Bookings Cron Job
 * Run this via cron or scheduled task
 * Example: */30 * * * * php /path/to/api/auto-confirm-bookings.php
 */

require_once __DIR__ . '/../config/config.php';

$pdo = getDBConnection();

// Get auto confirmation settings
$stmt = $pdo->query("SELECT * FROM auto_confirmation_settings LIMIT 1");
$settings = $stmt->fetch();

if (!$settings || !$settings['is_enabled']) {
    exit("Auto confirmation is disabled\n");
}

$hours_before = $settings['auto_confirm_hours'];

// Get pending bookings that should be auto-confirmed
$stmt = $pdo->prepare("
    SELECT b.*
    FROM bookings b
    WHERE b.status = 'pending'
    AND DATE_ADD(NOW(), INTERVAL ? HOUR) >= CONCAT(b.booking_date, ' ', b.booking_time)
");
$stmt->execute([$hours_before]);
$bookings = $stmt->fetchAll();

$confirmed = 0;
foreach ($bookings as $booking) {
    // Update status
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
    if ($stmt->execute([$booking['id']])) {
        // Create notification
        $stmt = $pdo->prepare("
            INSERT INTO notifications (user_id, booking_id, type, title, message)
            VALUES (?, ?, 'booking_confirmed', 'Lịch đặt đã được xác nhận tự động', ?)
        ");
        $message = "Lịch đặt của bạn đã được xác nhận tự động. Vui lòng đến đúng giờ! Bạn có thể đánh giá dịch vụ sau khi sử dụng.";
        $stmt->execute([$booking['user_id'], $booking['id'], $message]);
        
        $confirmed++;
    }
}

echo "Auto-confirmed $confirmed bookings\n";
?>

