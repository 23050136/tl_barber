<?php
$page_title = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$admin = checkAdminAuth();
$pdo = getDBConnection();

// Get statistics
$stats = [];

// Total bookings
$stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings");
$stats['total_bookings'] = $stmt->fetch()['count'];

// Pending bookings
$stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
$stats['pending_bookings'] = $stmt->fetch()['count'];

// Today's bookings
$stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings WHERE booking_date = CURDATE()");
$stats['today_bookings'] = $stmt->fetch()['count'];

// Total services
$stmt = $pdo->query("SELECT COUNT(*) as count FROM services");
$stats['total_services'] = $stmt->fetch()['count'];

// Total barbers
$stmt = $pdo->query("SELECT COUNT(*) as count FROM barbers WHERE is_available = 1");
$stats['total_barbers'] = $stmt->fetch()['count'];

// Total customers
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
$stats['total_customers'] = $stmt->fetch()['count'];

// Total revenue (completed bookings)
$stmt = $pdo->query("
    SELECT SUM(s.price) as revenue 
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    WHERE b.status = 'completed'
");
$stats['total_revenue'] = $stmt->fetch()['revenue'] ?? 0;

// Recent bookings
$stmt = $pdo->query("
    SELECT b.*, s.name as service_name, u.full_name as customer_name, u2.full_name as barber_name
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN users u ON b.user_id = u.id
    JOIN barbers bar ON b.barber_id = bar.id
    JOIN users u2 ON bar.user_id = u2.id
    ORDER BY b.created_at DESC
    LIMIT 10
");
$recent_bookings = $stmt->fetchAll();
?>

<div class="section">
    <div class="container">
        <h1 style="color: var(--primary-color); margin-bottom: 2rem;">
            <i class="fas fa-tachometer-alt"></i> Admin Dashboard
        </h1>
        
        <!-- Statistics Cards -->
        <div class="services-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 3rem;">
            <div class="card" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%); color: white;">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="font-size: 2.5rem; margin: 0;"><?php echo $stats['total_bookings']; ?></h3>
                            <p style="margin: 0; opacity: 0.9;">Tổng đặt lịch</p>
                        </div>
                        <i class="fas fa-calendar-check" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, var(--warning-color) 0%, #ff9800 100%); color: white;">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="font-size: 2.5rem; margin: 0;"><?php echo $stats['pending_bookings']; ?></h3>
                            <p style="margin: 0; opacity: 0.9;">Chờ xác nhận</p>
                        </div>
                        <i class="fas fa-clock" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, var(--accent-color) 0%, #0056b3 100%); color: white;">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="font-size: 2.5rem; margin: 0;"><?php echo $stats['today_bookings']; ?></h3>
                            <p style="margin: 0; opacity: 0.9;">Hôm nay</p>
                        </div>
                        <i class="fas fa-calendar-day" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, var(--success-color) 0%, #218838 100%); color: white;">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="font-size: 2.5rem; margin: 0;"><?php echo formatPrice($stats['total_revenue']); ?></h3>
                            <p style="margin: 0; opacity: 0.9;">Doanh thu</p>
                        </div>
                        <i class="fas fa-money-bill-wave" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-body">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Thao tác nhanh</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <a href="<?php echo BASE_URL; ?>admin/services.php" class="btn btn-primary">
                        <i class="fas fa-cut"></i> Quản lý dịch vụ
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/barbers.php" class="btn btn-primary">
                        <i class="fas fa-user-tie"></i> Quản lý barber
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/bookings.php" class="btn btn-primary">
                        <i class="fas fa-calendar"></i> Quản lý đặt lịch
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/reviews.php" class="btn btn-primary">
                        <i class="fas fa-star"></i> Quản lý đánh giá
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/statistics.php" class="btn btn-primary">
                        <i class="fas fa-chart-line"></i> Quản lý thống kê
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/settings.php" class="btn btn-secondary">
                        <i class="fas fa-cog"></i> Cài đặt
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Bookings -->
        <div class="card">
            <div class="card-body">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Đặt lịch gần đây</h2>
                <div class="booking-list">
                    <?php if (empty($recent_bookings)): ?>
                        <p style="text-align: center; color: var(--text-light); padding: 2rem;">
                            Chưa có đặt lịch nào
                        </p>
                    <?php else: ?>
                        <?php foreach ($recent_bookings as $booking): ?>
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
                                        <i class="fas fa-calendar"></i> <?php echo formatDate($booking['booking_date']); ?> 
                                        lúc <?php echo date('H:i', strtotime($booking['booking_time'])); ?>
                                    </p>
                                </div>
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
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

