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

// Check if reply columns exist
try {
    $pdo->query("SELECT reply, reply_at FROM reviews LIMIT 1");
    $reply_columns_exist = true;
} catch (PDOException $e) {
    $reply_columns_exist = false;
}

// Get user bookings with reviews
if ($reply_columns_exist) {
    $stmt = $pdo->prepare("
        SELECT b.*, s.name as service_name, s.price, s.duration,
               u2.full_name as barber_name,
               r.id as review_id, r.rating, r.comment as review_comment, 
               r.reply as admin_reply, r.reply_at, r.created_at as review_created_at
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN barbers bar ON b.barber_id = bar.id
        JOIN users u2 ON bar.user_id = u2.id
        LEFT JOIN reviews r ON b.id = r.booking_id
        WHERE b.user_id = ?
        ORDER BY b.booking_date DESC, b.booking_time DESC
    ");
} else {
    // Fallback query without reply columns
    $stmt = $pdo->prepare("
        SELECT b.*, s.name as service_name, s.price, s.duration,
               u2.full_name as barber_name,
               r.id as review_id, r.rating, r.comment as review_comment, 
               r.created_at as review_created_at,
               NULL as admin_reply, NULL as reply_at
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN barbers bar ON b.barber_id = bar.id
        JOIN users u2 ON bar.user_id = u2.id
        LEFT JOIN reviews r ON b.id = r.booking_id
        WHERE b.user_id = ?
        ORDER BY b.booking_date DESC, b.booking_time DESC
    ");
}
$stmt->execute([$current_user['id']]);
$bookings = $stmt->fetchAll();

// Generate QR codes for confirmed bookings that don't have one
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
    }
}

// Show notice if reply columns don't exist
if (!$reply_columns_exist) {
    $error = '⚠️ Vui lòng cập nhật database để sử dụng tính năng phản hồi đánh giá. <a href="' . BASE_URL . 'add_reply_columns.php" style="color: white; text-decoration: underline; font-weight: bold;">Nhấn vào đây để cập nhật</a>';
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
                                <?php if (empty($booking['payment_status'] ?? '') || ($booking['payment_status'] ?? '') !== 'paid'): ?>
                                    <a href="<?php echo BASE_URL; ?>pages/payment.php?booking_id=<?php echo $booking['id']; ?>" 
                                       class="btn btn-primary" style="font-size: 0.9rem; padding: 8px 15px; margin-top: 0.5rem;">
                                        <i class="fas fa-credit-card"></i> Thanh toán
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($booking['qr_code'] ?? '')): ?>
                                    <a href="<?php echo BASE_URL; ?>pages/qr-display.php?code=<?php echo urlencode($booking['qr_code']); ?>" 
                                       class="btn btn-secondary" style="font-size: 0.9rem; padding: 8px 15px; margin-top: 0.5rem;">
                                        <i class="fas fa-qrcode"></i> Mã QR
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php
                            // Cho phép đánh giá ngay sau khi admin đã duyệt lịch (confirmed)
                            // hoặc khi đã hoàn thành (completed)
                            $can_review = in_array($booking['status'], ['confirmed', 'completed']);
                            ?>
                            <?php if ($can_review): ?>
                                <?php
                                $stmt = $pdo->prepare("SELECT id FROM reviews WHERE booking_id = ?");
                                $stmt->execute([$booking['id']]);
                                $has_review = $stmt->fetch();
                                ?>
                                <?php if (!$has_review): ?>
                                    <a href="<?php echo BASE_URL; ?>pages/review.php?booking_id=<?php echo $booking['id']; ?>" 
                                       class="btn btn-success" style="font-size: 0.9rem; padding: 8px 15px; margin-top: 0.5rem;">
                                        <i class="fas fa-star"></i> Đánh giá
                                    </a>
                                <?php else: ?>
                                    <span style="color: var(--success-color); font-size: 0.9rem;">
                                        <i class="fas fa-check"></i> Đã đánh giá
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Feedback Section -->
                        <?php if (!empty($booking['review_id'])): ?>
                            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid var(--border-color);">
                                <h4 style="color: var(--primary-color); margin-bottom: 1rem;">
                                    <i class="fas fa-star"></i> Feedback của bạn
                                </h4>
                                
                                <!-- User Review -->
                                <div style="background: var(--bg-light); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                                        <div style="display: flex; gap: 0.25rem;">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= $booking['rating']): ?>
                                                    <i class="fas fa-star" style="color: var(--secondary-color);"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star" style="color: var(--text-light);"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                        <span style="color: var(--text-light); font-size: 0.85rem;">
                                            <?php echo formatDateTime($booking['review_created_at']); ?>
                                        </span>
                                    </div>
                                    <p style="color: var(--text-color); margin: 0; line-height: 1.6;">
                                        "<?php echo htmlspecialchars($booking['review_comment']); ?>"
                                    </p>
                                </div>
                                
                                <!-- Admin Reply -->
                                <?php if (!empty($booking['admin_reply'])): ?>
                                    <div style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white; padding: 1rem; border-radius: 8px; margin-left: 2rem; position: relative;">
                                        <div style="position: absolute; left: -10px; top: 15px; width: 0; height: 0; border-top: 10px solid transparent; border-bottom: 10px solid transparent; border-right: 10px solid var(--primary-color);"></div>
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                            <i class="fas fa-store" style="font-size: 1.2rem;"></i>
                                            <strong>TL Barber</strong>
                                            <span style="font-size: 0.85rem; opacity: 0.9; margin-left: auto;">
                                                <?php echo formatDateTime($booking['reply_at']); ?>
                                            </span>
                                        </div>
                                        <p style="margin: 0; line-height: 1.6;">
                                            <?php echo nl2br(htmlspecialchars($booking['admin_reply'])); ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

