<?php
$page_title = 'Dịch vụ';
require_once __DIR__ . '/../includes/header.php';

$pdo = getDBConnection();
$stmt = $pdo->query("SELECT * FROM services ORDER BY is_featured DESC, name ASC");
$services = $stmt->fetchAll();
?>

<section class="section">
    <div class="container">
        <h2 class="section-title">Tất cả dịch vụ</h2>
        <p class="text-center" style="color: var(--text-light); margin-bottom: 3rem; font-size: 1.1rem;">
            Chúng tôi cung cấp đầy đủ các dịch vụ chăm sóc tóc và làm đẹp chuyên nghiệp
        </p>
        
        <div class="services-grid">
            <?php foreach ($services as $service): ?>
                <div class="card service-card">
                    <?php if ($service['is_featured']): ?>
                        <div style="position: absolute; top: 15px; left: 15px; background: var(--secondary-color); color: var(--primary-color); padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; z-index: 1;">
                            <i class="fas fa-star"></i> Nổi bật
                        </div>
                    <?php endif; ?>
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
                            <i class="fas fa-clock"></i> Thời lượng: <?php echo $service['duration']; ?> phút
                        </div>
                        <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                            <a href="<?php echo BASE_URL; ?>pages/service-detail.php?id=<?php echo $service['id']; ?>" 
                               class="btn btn-outline" style="flex: 1;">
                                <i class="fas fa-info-circle"></i> Chi tiết
                            </a>
                            <a href="<?php echo BASE_URL; ?>pages/booking.php?service_id=<?php echo $service['id']; ?>" 
                               class="btn btn-primary" style="flex: 1;">
                                <i class="fas fa-calendar"></i> Đặt lịch
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

