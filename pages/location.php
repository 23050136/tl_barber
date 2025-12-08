<?php
$page_title = 'Địa chỉ TL Barber';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="section">
    <div class="container location-wrapper">
        <div class="section-title">Địa chỉ TL Barber</div>

        <div class="location-grid">
            <div class="card location-info">
                <div class="card-body">
                    <h2 class="location-heading"><i class="fas fa-map-marker-alt"></i> Thông tin tiệm</h2>
                    <ul class="location-list">
                        <li>
                            <span class="location-label"><i class="fas fa-store"></i> Tên tiệm:</span>
                            <span class="location-value">TL Barber</span>
                        </li>
                        <li>
                            <span class="location-label"><i class="fas fa-location-dot"></i> Địa chỉ:</span>
                            <span class="location-value">504, phú lợi , tp hồ chí minh</span>
                        </li>
                        <li>
                            <span class="location-label"><i class="fas fa-clock"></i> Thời gian hoạt động:</span>
                            <span class="location-value">Thứ 2 – Chủ nhật (8:00 – 20:00)</span>
                        </li>
                        <li>
                            <span class="location-label"><i class="fas fa-phone"></i> Số điện thoại:</span>
                            <span class="location-value"><a href="tel:098556089">098556089</a></span>
                        </li>
                        <li>
                            <span class="location-label"><i class="fas fa-envelope"></i> Email:</span>
                            <span class="location-value"><a href="mailto:tlbarber@gmail.com">tlbarber@gmail.com</a></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card location-map">
                <div class="card-body">
                    <h2 class="location-heading"><i class="fas fa-map"></i> Bản đồ</h2>
                    <div class="map-embed">
                        <iframe
                            src="https://www.google.com/maps?q=504%20Ph%C3%BA%20L%E1%BB%A3i%2C%20TP%20H%E1%BB%93%20Ch%C3%AD%20Minh&output=embed"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


