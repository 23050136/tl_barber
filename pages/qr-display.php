<?php
$page_title = 'Mã QR Check-in';
require_once __DIR__ . '/../includes/header.php';

if (!$current_user) {
    redirect(BASE_URL . 'pages/login.php');
}

$code = $_GET['code'] ?? '';

if (empty($code)) {
    redirect(BASE_URL . 'pages/booking-history.php');
}

$pdo = getDBConnection();

// Get booking
$stmt = $pdo->prepare("
    SELECT b.*, s.name as service_name
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    WHERE b.qr_code = ? AND b.user_id = ?
");
$stmt->execute([$code, $current_user['id']]);
$booking = $stmt->fetch();

if (!$booking) {
    redirect(BASE_URL . 'pages/booking-history.php');
}
?>

<section class="section">
    <div class="container">
        <div class="form-container" style="max-width: 500px; text-align: center;">
            <h2 style="color: var(--primary-color); margin-bottom: 1rem;">
                <i class="fas fa-qrcode"></i> Mã QR Check-in
            </h2>
            
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <div style="background: white; padding: 2rem; border: 2px solid var(--primary-color); border-radius: 10px; margin-bottom: 1rem;">
                        <div style="font-family: monospace; font-size: 1.5rem; font-weight: bold; color: var(--primary-color); word-break: break-all;">
                            <?php echo htmlspecialchars($code); ?>
                        </div>
                    </div>
                    <p style="color: var(--text-light); margin-bottom: 0.5rem;">
                        <strong><?php echo htmlspecialchars($booking['service_name']); ?></strong>
                    </p>
                    <p style="color: var(--text-light); font-size: 0.9rem;">
                        <?php echo formatDate($booking['booking_date']); ?> 
                        lúc <?php echo date('H:i', strtotime($booking['booking_time'])); ?>
                    </p>
                </div>
            </div>
            
            <p style="color: var(--text-light); font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i> Vui lòng trình mã này tại cửa hàng để check-in
            </p>
            
            <a href="<?php echo BASE_URL; ?>pages/booking-history.php" class="btn btn-outline" style="margin-top: 1rem;">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

