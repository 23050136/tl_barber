<?php
$page_title = 'Trang chủ';
require_once __DIR__ . '/includes/header.php';

$pdo = getDBConnection();

// Get featured services
$stmt = $pdo->query("SELECT * FROM services WHERE is_featured = 1 ORDER BY id LIMIT 6");
$featured_services = $stmt->fetchAll();

// Get featured reviews
$stmt = $pdo->query("
    SELECT r.*, u.full_name, s.name as service_name, b.user_id as barber_user_id
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN services s ON r.service_id = s.id
    JOIN barbers b ON r.barber_id = b.id
    ORDER BY r.created_at DESC
    LIMIT 6
");
$featured_reviews = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Chào mừng đến với TL Barber</h1>
        <p>Dịch vụ cắt tóc và làm đẹp chuyên nghiệp, mang đến cho bạn vẻ ngoài tự tin và phong cách độc đáo</p>
        <a href="<?php echo BASE_URL; ?>pages/booking.php" class="btn btn-primary">
            <i class="fas fa-calendar-check"></i> Đặt lịch ngay
        </a>
    </div>
</section>

<!-- Featured Services Section -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Dịch vụ nổi bật</h2>
        <div class="services-grid">
            <?php foreach ($featured_services as $service): ?>
                <div class="card service-card">
                    <div class="card-img">
                        <?php if (!empty($service['image'])): ?>
                            <img src="<?php echo getImageUrl($service['image']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" style="width:100%; height:100%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-cut"></i>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="service-price"><?php echo formatPrice($service['price']); ?></div>
                        <h3 class="card-title"><?php echo htmlspecialchars($service['name']); ?></h3>
                        <p class="card-text"><?php echo htmlspecialchars($service['description']); ?></p>
                        <div class="service-duration">
                            <i class="fas fa-clock"></i> <?php echo $service['duration']; ?> phút
                        </div>
                        <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                            <a href="<?php echo BASE_URL; ?>pages/service-detail.php?id=<?php echo $service['id']; ?>" 
                               class="btn btn-outline" style="flex: 1;">
                                Xem chi tiết
                            </a>
                            <a href="<?php echo BASE_URL; ?>pages/booking.php?service_id=<?php echo $service['id']; ?>" 
                               class="btn btn-primary" style="flex: 1;">
                                Đặt lịch
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-3">
            <a href="<?php echo BASE_URL; ?>pages/services.php" class="btn btn-secondary">
                Xem tất cả dịch vụ <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section" style="background: var(--bg-white);">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center;">
            <div>
                <h2 class="section-title" style="text-align: left; margin-bottom: 1.5rem;">
                    Vì sao chọn TL Barber?
                </h2>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 1rem; display: flex; align-items: start; gap: 1rem;">
                        <i class="fas fa-check-circle" style="color: var(--success-color); font-size: 1.5rem;"></i>
                        <div>
                            <strong>Đội ngũ barber chuyên nghiệp</strong>
                            <p style="color: var(--text-light); margin-top: 0.25rem;">
                                Với nhiều năm kinh nghiệm và tay nghề cao
                            </p>
                        </div>
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; align-items: start; gap: 1rem;">
                        <i class="fas fa-check-circle" style="color: var(--success-color); font-size: 1.5rem;"></i>
                        <div>
                            <strong>Sản phẩm chất lượng cao</strong>
                            <p style="color: var(--text-light); margin-top: 0.25rem;">
                                Sử dụng các sản phẩm chăm sóc tóc uy tín, an toàn
                            </p>
                        </div>
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; align-items: start; gap: 1rem;">
                        <i class="fas fa-check-circle" style="color: var(--success-color); font-size: 1.5rem;"></i>
                        <div>
                            <strong>Đặt lịch dễ dàng</strong>
                            <p style="color: var(--text-light); margin-top: 0.25rem;">
                                Hệ thống đặt lịch online tiện lợi, nhanh chóng
                            </p>
                        </div>
                    </li>
                    <li style="margin-bottom: 1rem; display: flex; align-items: start; gap: 1rem;">
                        <i class="fas fa-check-circle" style="color: var(--success-color); font-size: 1.5rem;"></i>
                        <div>
                            <strong>Giá cả hợp lý</strong>
                            <p style="color: var(--text-light); margin-top: 0.25rem;">
                                Nhiều gói dịch vụ với mức giá phù hợp
                            </p>
                        </div>
                    </li>
                </ul>
            </div>
            <div style="text-align: center;">
                <div style="background: var(--primary-color); color: white; padding: 3rem; border-radius: 10px;">
                    <i class="fas fa-cut" style="font-size: 5rem; color: var(--secondary-color); margin-bottom: 1rem;"></i>
                    <h3 style="font-size: 2rem; margin-bottom: 1rem;">Hơn 1000+</h3>
                    <p style="font-size: 1.2rem; opacity: 0.9;">Khách hàng hài lòng</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<?php if (!empty($featured_reviews)): ?>
<section class="section">
    <div class="container">
        <h2 class="section-title">Đánh giá từ khách hàng</h2>
        <div class="reviews-grid">
            <?php foreach ($featured_reviews as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-avatar">
                            <?php echo strtoupper(substr($review['full_name'], 0, 1)); ?>
                        </div>
                        <div style="flex: 1;">
                            <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                            <div class="review-rating">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $review['rating']) {
                                        echo '<i class="fas fa-star"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <p class="review-text">"<?php echo htmlspecialchars($review['comment']); ?>"</p>
                    <small style="color: var(--text-light);">
                        <i class="fas fa-cut"></i> <?php echo htmlspecialchars($review['service_name']); ?>
                    </small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="section" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%); color: white;">
    <div class="container text-center">
        <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">Sẵn sàng thay đổi diện mạo?</h2>
        <p style="font-size: 1.25rem; margin-bottom: 2rem; opacity: 0.9;">
            Đặt lịch ngay hôm nay để nhận được dịch vụ tốt nhất
        </p>
        <a href="<?php echo BASE_URL; ?>pages/booking.php" class="btn btn-primary" style="font-size: 1.1rem; padding: 15px 40px;">
            <i class="fas fa-calendar-check"></i> Đặt lịch ngay
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

