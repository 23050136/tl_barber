<?php
$page_title = 'Lịch sử đặt lịch';
require_once __DIR__ . '/../includes/header.php';

if (!$current_user) {
    redirect(BASE_URL . 'pages/login.php');
}

$pdo = getDBConnection();
$error = '';
$success = '';

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'] ?? 0;
    
    if ($booking_id) {
        // Check if booking belongs to user and can be cancelled
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
        $stmt->execute([$booking_id, $current_user['id']]);
        $booking = $stmt->fetch();
        
        if ($booking && in_array($booking['status'], ['pending', 'confirmed'])) {
            // Check if booking is at least 2 hours away
            $booking_datetime = strtotime($booking['booking_date'] . ' ' . $booking['booking_time']);
            $current_datetime = time();
            $hours_diff = ($booking_datetime - $current_datetime) / 3600;
            
            if ($hours_diff >= 2) {
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
                if ($stmt->execute([$booking_id])) {
                    // Create notification
                    $stmt = $pdo->prepare("
                        INSERT INTO notifications (user_id, booking_id, type, title, message)
                        VALUES (?, ?, 'booking_cancelled', 'Hủy lịch thành công', ?)
                    ");
                    $message = "Bạn đã hủy lịch đặt thành công. Chúng tôi rất tiếc vì không thể phục vụ bạn lần này.";
                    $stmt->execute([$current_user['id'], $booking_id, $message]);
                    
                    $success = 'Hủy lịch thành công';
                } else {
                    $error = 'Có lỗi xảy ra';
                }
            } else {
                $error = 'Chỉ có thể hủy lịch trước 2 giờ. Vui lòng liên hệ trực tiếp.';
            }
        } else {
            $error = 'Không thể hủy lịch này';
        }
    }
}

// Get user bookings
$stmt = $pdo->prepare("
    SELECT b.*, s.name as service_name, s.price, s.duration,
           u2.full_name as barber_name
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN barbers bar ON b.barber_id = bar.id
    JOIN users u2 ON bar.user_id = u2.id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC, b.booking_time DESC
");
$stmt->execute([$current_user['id']]);
$bookings = $stmt->fetchAll();

// Generate QR codes for confirmed bookings that don't have one
<<<<<<< HEAD
foreach ($bookings as $booking) {
    if (in_array($booking['status'], ['confirmed', 'pending']) && empty($booking['qr_code'])) {
        $qr_code = 'BOOKING-' . $booking['id'] . '-' . time();
        $stmt = $pdo->prepare("UPDATE bookings SET qr_code = ? WHERE id = ?");
        $stmt->execute([$qr_code, $booking['id']]);
=======
// First check if qr_code column exists
try {
    $pdo->query("SELECT qr_code FROM bookings LIMIT 1");
    $qr_column_exists = true;
} catch (PDOException $e) {
    $qr_column_exists = false;
}

if ($qr_column_exists) {
    foreach ($bookings as $booking) {
        if (in_array($booking['status'], ['confirmed', 'pending']) && empty($booking['qr_code'] ?? '')) {
            $qr_code = 'BOOKING-' . $booking['id'] . '-' . time();
            $stmt = $pdo->prepare("UPDATE bookings SET qr_code = ? WHERE id = ?");
            $stmt->execute([$qr_code, $booking['id']]);
        }
>>>>>>> e906b55 (update code)
    }
}
?>

<section class="section">
    <div class="container">
        <h2 class="section-title">Lịch sử đặt lịch</h2>
        
        <?php if ($error): ?>
            <div class="alert" style="background: var(--danger-color); color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert" style="background: var(--success-color); color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($bookings)): ?>
            <div class="card" style="text-align: center; padding: 3rem;">
                <i class="fas fa-calendar-times" style="font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                <h3 style="color: var(--text-light); margin-bottom: 1rem;">Chưa có lịch đặt nào</h3>
                <a href="<?php echo BASE_URL; ?>pages/booking.php" class="btn btn-primary">
                    <i class="fas fa-calendar-check"></i> Đặt lịch ngay
                </a>
            </div>
        <?php else: ?>
            <div class="booking-list">
                <?php foreach ($bookings as $booking): ?>
                    <div class="booking-item">
                        <div class="booking-info">
                            <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                                <?php echo htmlspecialchars($booking['service_name']); ?>
                            </h3>
                            <p style="margin-bottom: 0.5rem;">
                                <i class="fas fa-user-tie"></i> Barber: <strong><?php echo htmlspecialchars($booking['barber_name']); ?></strong>
                            </p>
                            <p style="margin-bottom: 0.5rem;">
                                <i class="fas fa-calendar"></i> Ngày: <strong><?php echo formatDate($booking['booking_date']); ?></strong>
                            </p>
                            <p style="margin-bottom: 0.5rem;">
                                <i class="fas fa-clock"></i> Giờ: <strong><?php echo date('H:i', strtotime($booking['booking_time'])); ?></strong>
                            </p>
                            <p style="margin-bottom: 0.5rem;">
                                <i class="fas fa-money-bill-wave"></i> Giá: <strong><?php echo formatPrice($booking['price']); ?></strong>
                            </p>
                            <?php if ($booking['notes']): ?>
                                <p style="color: var(--text-light); font-size: 0.9rem; margin-top: 0.5rem;">
                                    <i class="fas fa-sticky-note"></i> <?php echo htmlspecialchars($booking['notes']); ?>
                                </p>
                            <?php endif; ?>
                            <p style="color: var(--text-light); font-size: 0.85rem; margin-top: 0.5rem;">
                                Đặt lúc: <?php echo formatDateTime($booking['created_at']); ?>
                            </p>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-end;">
                            <span class="booking-status status-<?php echo $booking['status']; ?>">
                                <?php
                                $status_text = [
                                    'pending' => 'Chờ xác nhận',
                                    'confirmed' => 'Đã xác nhận',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy'
                                ];
                                echo $status_text[$booking['status']];
                                ?>
                            </span>
                            
                            <?php if (in_array($booking['status'], ['pending', 'confirmed'])): ?>
                                <?php
                                $booking_datetime = strtotime($booking['booking_date'] . ' ' . $booking['booking_time']);
                                $current_datetime = time();
                                $hours_diff = ($booking_datetime - $current_datetime) / 3600;
                                ?>
                                <?php if ($hours_diff >= 2): ?>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn hủy lịch này?');">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" name="cancel_booking" class="btn btn-danger" style="font-size: 0.9rem; padding: 8px 15px;">
                                            <i class="fas fa-times"></i> Hủy lịch
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if (in_array($booking['status'], ['confirmed', 'pending'])): ?>
<<<<<<< HEAD
                                <?php if (empty($booking['payment_status']) || $booking['payment_status'] !== 'paid'): ?>
=======
                                <?php if (empty($booking['payment_status'] ?? '') || ($booking['payment_status'] ?? '') !== 'paid'): ?>
>>>>>>> e906b55 (update code)
                                    <a href="<?php echo BASE_URL; ?>pages/payment.php?booking_id=<?php echo $booking['id']; ?>" 
                                       class="btn btn-primary" style="font-size: 0.9rem; padding: 8px 15px; margin-top: 0.5rem;">
                                        <i class="fas fa-credit-card"></i> Thanh toán
                                    </a>
                                <?php endif; ?>
<<<<<<< HEAD
                                <?php if ($booking['qr_code']): ?>
=======
                                <?php if (!empty($booking['qr_code'] ?? '')): ?>
>>>>>>> e906b55 (update code)
                                    <a href="<?php echo BASE_URL; ?>pages/qr-display.php?code=<?php echo urlencode($booking['qr_code']); ?>" 
                                       class="btn btn-secondary" style="font-size: 0.9rem; padding: 8px 15px; margin-top: 0.5rem;">
                                        <i class="fas fa-qrcode"></i> Mã QR
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if ($booking['status'] === 'completed'): ?>
                                <?php
                                // Check if user has already reviewed
                                $stmt = $pdo->prepare("SELECT id FROM reviews WHERE booking_id = ?");
                                $stmt->execute([$booking['id']]);
                                $has_review = $stmt->fetch();
                                ?>
                                <?php if (!$has_review): ?>
                                    <a href="<?php echo BASE_URL; ?>pages/review.php?booking_id=<?php echo $booking['id']; ?>" 
                                       class="btn btn-success" style="font-size: 0.9rem; padding: 8px 15px;">
                                        <i class="fas fa-star"></i> Đánh giá
                                    </a>
                                <?php else: ?>
                                    <span style="color: var(--success-color); font-size: 0.9rem;">
                                        <i class="fas fa-check"></i> Đã đánh giá
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

