<?php
$page_title = 'Chi tiết dịch vụ';
require_once __DIR__ . '/../includes/header.php';

$service_id = $_GET['id'] ?? 0;

if (!$service_id) {
    redirect(BASE_URL . 'pages/services.php');
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch();

if (!$service) {
    redirect(BASE_URL . 'pages/services.php');
}

// Get barbers who can perform this service
$stmt = $pdo->prepare("
    SELECT b.*, u.full_name, u.phone
    FROM barbers b
    JOIN users u ON b.user_id = u.id
    JOIN service_barber sb ON b.id = sb.barber_id
    WHERE sb.service_id = ? AND b.is_available = 1
");
$stmt->execute([$service_id]);
$barbers = $stmt->fetchAll();

// Get reviews for this service
$stmt = $pdo->prepare("
    SELECT r.*, u.full_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.service_id = ?
    ORDER BY r.created_at DESC
    LIMIT 10
");
$stmt->execute([$service_id]);
$reviews = $stmt->fetchAll();

// Calculate average rating
$stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE service_id = ?");
$stmt->execute([$service_id]);
$rating_data = $stmt->fetch();
$avg_rating = round($rating_data['avg_rating'] ?? 0, 1);
$total_reviews = $rating_data['total_reviews'] ?? 0;
?>

<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 3rem;">
            <div>
                <div class="card-img" style="height: 400px;">
                    <?php if (!empty($service['image'])): ?>
                        <img src="<?php echo getImageUrl($service['image']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" style="width:100%; height:100%; object-fit: cover;">
                    <?php else: ?>
                        <i class="fas fa-cut" style="font-size: 5rem;"></i>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <h1 style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($service['name']); ?>
                </h1>
                
                <?php if ($total_reviews > 0): ?>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="review-rating" style="font-size: 1.5rem;">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= floor($avg_rating)) {
                                    echo '<i class="fas fa-star"></i>';
                                } elseif ($i - 0.5 <= $avg_rating) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <span style="font-size: 1.2rem; font-weight: bold;"><?php echo $avg_rating; ?></span>
                        <span style="color: var(--text-light);">(<?php echo $total_reviews; ?> đánh giá)</span>
                    </div>
                <?php endif; ?>
                
                <div style="background: var(--bg-light); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <strong style="color: var(--text-light);">Giá dịch vụ:</strong>
                        <span style="font-size: 1.5rem; font-weight: bold; color: var(--primary-color);">
                            <?php echo formatPrice($service['price']); ?>
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <strong style="color: var(--text-light);">Thời lượng:</strong>
                        <span style="font-size: 1.2rem;">
                            <i class="fas fa-clock"></i> <?php echo $service['duration']; ?> phút
                        </span>
                    </div>
                </div>
                
                <p style="color: var(--text-light); line-height: 1.8; margin-bottom: 2rem; font-size: 1.1rem;">
                    <?php echo nl2br(htmlspecialchars($service['description'])); ?>
                </p>
                
                <a href="<?php echo BASE_URL; ?>pages/booking.php?service_id=<?php echo $service['id']; ?>" 
                   class="btn btn-primary" style="font-size: 1.1rem; padding: 15px 40px;">
                    <i class="fas fa-calendar-check"></i> Đặt lịch ngay
                </a>
            </div>
        </div>
        
        <!-- Barbers Section -->
        <div style="margin-bottom: 3rem;">
            <h2 class="section-title" style="font-size: 2rem; margin-bottom: 2rem;">Barber thực hiện</h2>
            <?php if (!empty($barbers)): ?>
                <div class="services-grid">
                    <?php foreach ($barbers as $barber): ?>
                        <div class="card">
                            <div class="card-body">
                                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                    <div class="review-avatar" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                        <?php echo strtoupper(substr($barber['full_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h3 style="margin: 0; color: var(--primary-color);"><?php echo htmlspecialchars($barber['full_name']); ?></h3>
                                        <div class="review-rating">
                                            <?php
                                            $barber_rating = round($barber['rating'], 1);
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= floor($barber_rating)) {
                                                    echo '<i class="fas fa-star"></i>';
                                                } else {
                                                    echo '<i class="far fa-star"></i>';
                                                }
                                            }
                                            ?>
                                            <span style="margin-left: 0.5rem;"><?php echo $barber_rating; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($barber['specialization']): ?>
                                    <p style="color: var(--text-light); margin-bottom: 0.5rem;">
                                        <i class="fas fa-star"></i> <?php echo htmlspecialchars($barber['specialization']); ?>
                                    </p>
                                <?php endif; ?>
                                <p style="color: var(--text-light); font-size: 0.9rem;">
                                    <i class="fas fa-briefcase"></i> <?php echo $barber['experience_years']; ?> năm kinh nghiệm
                                    <span style="margin-left: 1rem;">
                                        <i class="fas fa-comments"></i> <?php echo $barber['total_reviews']; ?> đánh giá
                                    </span>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: var(--text-light); padding: 2rem;">
                    Hiện chưa có barber nào cho dịch vụ này
                </p>
            <?php endif; ?>
        </div>
        
        <!-- Reviews Section -->
        <?php if (!empty($reviews)): ?>
            <div>
                <h2 class="section-title" style="font-size: 2rem; margin-bottom: 2rem;">Đánh giá khách hàng</h2>
                <div class="reviews-grid">
                    <?php foreach ($reviews as $review): ?>
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
                                <?php echo formatDate($review['created_at']); ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

