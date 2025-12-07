<?php
$page_title = 'QR Check-in';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$admin = checkAdminAuth();
$pdo = getDBConnection();
$error = '';
$success = '';

// Handle QR scan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qr_code = sanitize($_POST['qr_code'] ?? '');
    
    if (empty($qr_code)) {
        $error = 'Vui lòng nhập mã QR';
    } else {
        // Find booking by QR code
        $stmt = $pdo->prepare("
            SELECT b.*, s.name as service_name,
                   u.full_name as customer_name, u2.full_name as barber_name
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            JOIN users u ON b.user_id = u.id
            JOIN barbers bar ON b.barber_id = bar.id
            JOIN users u2 ON bar.user_id = u2.id
            WHERE b.qr_code = ? AND b.status IN ('pending', 'confirmed')
        ");
        $stmt->execute([$qr_code]);
        $booking = $stmt->fetch();
        
        if ($booking) {
            // Check if booking is today
            if ($booking['booking_date'] == date('Y-m-d')) {
                // Check in
                $stmt = $pdo->prepare("
                    UPDATE bookings 
                    SET check_in_time = NOW() 
                    WHERE id = ?
                ");
                if ($stmt->execute([$booking['id']])) {
                    $success = "Check-in thành công cho khách hàng: {$booking['customer_name']}";
                } else {
                    $error = 'Có lỗi xảy ra khi check-in';
                }
            } else {
                $error = 'Lịch đặt không phải hôm nay';
            }
        } else {
            $error = 'Mã QR không hợp lệ hoặc lịch đặt không tồn tại';
        }
    }
}

// Get today's check-ins
$stmt = $pdo->query("
    SELECT b.*, s.name as service_name,
           u.full_name as customer_name, u2.full_name as barber_name
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN users u ON b.user_id = u.id
    JOIN barbers bar ON b.barber_id = bar.id
    JOIN users u2 ON bar.user_id = u2.id
    WHERE b.booking_date = CURDATE() 
    AND b.status IN ('pending', 'confirmed')
    ORDER BY b.booking_time ASC
");
$today_bookings = $stmt->fetchAll();
?>

<div class="section">
    <div class="container">
        <h1 style="color: var(--primary-color); margin-bottom: 2rem;">
            <i class="fas fa-qrcode"></i> QR Check-in
        </h1>
        
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
        
        <!-- QR Scanner Form -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-body">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">
                    <i class="fas fa-barcode"></i> Quét mã QR
                </h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="qr_code">Mã QR Code</label>
                        <input type="text" id="qr_code" name="qr_code" class="form-control" 
                               placeholder="Nhập hoặc quét mã QR" required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-check"></i> Check-in
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Today's Bookings -->
        <div class="card">
            <div class="card-body">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">
                    <i class="fas fa-calendar-day"></i> Lịch đặt hôm nay
                </h2>
                <div class="booking-list">
                    <?php if (empty($today_bookings)): ?>
                        <p style="text-align: center; color: var(--text-light); padding: 2rem;">
                            Không có lịch đặt nào hôm nay
                        </p>
                    <?php else: ?>
                        <?php foreach ($today_bookings as $booking): ?>
                            <div class="booking-item">
                                <div class="booking-info">
                                    <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                                        <?php echo htmlspecialchars($booking['service_name']); ?>
                                    </h3>
                                    <p style="margin-bottom: 0.25rem;">
                                        <i class="fas fa-user"></i> Khách: <strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong>
                                    </p>
                                    <p style="margin-bottom: 0.25rem;">
                                        <i class="fas fa-user-tie"></i> Barber: <strong><?php echo htmlspecialchars($booking['barber_name']); ?></strong>
                                    </p>
                                    <p style="margin-bottom: 0.25rem;">
                                        <i class="fas fa-clock"></i> Giờ: <strong><?php echo date('H:i', strtotime($booking['booking_time'])); ?></strong>
                                    </p>
                                    <?php if ($booking['check_in_time']): ?>
                                        <p style="color: var(--success-color); margin-top: 0.5rem;">
                                            <i class="fas fa-check-circle"></i> Đã check-in lúc: <?php echo formatDateTime($booking['check_in_time']); ?>
                                        </p>
                                    <?php else: ?>
                                        <p style="color: var(--warning-color); margin-top: 0.5rem;">
                                            <i class="fas fa-clock"></i> Chưa check-in
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div>
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
                                    <?php if ($booking['qr_code']): ?>
                                        <div style="margin-top: 0.5rem; font-size: 0.85rem; color: var(--text-light);">
                                            QR: <?php echo htmlspecialchars($booking['qr_code']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

