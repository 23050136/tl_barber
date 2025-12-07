<?php
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$date = $_GET['date'] ?? '';
$barber_id = $_GET['barber_id'] ?? 0;

if (empty($date) || empty($barber_id)) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$pdo = getDBConnection();

// Get all time slots
$stmt = $pdo->query("SELECT start_time, end_time FROM time_slots WHERE is_active = 1 ORDER BY start_time");
$allSlots = $stmt->fetchAll();

// Get booked slots for the selected date and barber
$stmt = $pdo->prepare("
    SELECT booking_time 
    FROM bookings 
    WHERE barber_id = ? 
    AND booking_date = ? 
    AND status IN ('pending', 'confirmed')
");
$stmt->execute([$barber_id, $date]);
$bookedSlots = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Format booked slots
$bookedTimes = array_map(function($time) {
    return date('H:i', strtotime($time));
}, $bookedSlots);

// Get available slots
$availableSlots = [];
foreach ($allSlots as $slot) {
    $startTime = date('H:i', strtotime($slot['start_time']));
    if (!in_array($startTime, $bookedTimes)) {
        $availableSlots[] = $startTime;
    }
}

echo json_encode([
    'available_slots' => $availableSlots,
    'booked_slots' => $bookedTimes
]);
?>

