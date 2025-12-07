<?php
/**
 * Generate QR Code for booking
 * This should be called when booking is confirmed
 */

require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$booking_id = $_POST['booking_id'] ?? $_GET['booking_id'] ?? 0;

if (!$booking_id) {
    echo json_encode(['error' => 'Missing booking_id']);
    exit;
}

$pdo = getDBConnection();

// Get booking
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

if (!$booking) {
    echo json_encode(['error' => 'Booking not found']);
    exit;
}

// Generate QR code (simple format: BOOKING-{ID}-{TIMESTAMP})
$qr_code = 'BOOKING-' . $booking_id . '-' . time();

// Update booking with QR code
$stmt = $pdo->prepare("UPDATE bookings SET qr_code = ? WHERE id = ?");
if ($stmt->execute([$qr_code, $booking_id])) {
    echo json_encode([
        'success' => true,
        'qr_code' => $qr_code,
        'qr_url' => BASE_URL . 'pages/qr-display.php?code=' . $qr_code
    ]);
} else {
    echo json_encode(['error' => 'Failed to generate QR code']);
}
?>

