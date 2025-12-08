<?php
require_once __DIR__ . '/../config/config.php';
$current_user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="main-header">
        <nav class="navbar">
            <div class="container">
                <div class="nav-brand">
                    <a href="<?php echo BASE_URL; ?>index.php">
                        <i class="fas fa-cut"></i>
                        <span><?php echo SITE_NAME; ?></span>
                    </a>
                </div>
                <ul class="nav-menu">
                    <li><a href="<?php echo BASE_URL; ?>index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Trang chủ</a></li>
                    <li><a href="<?php echo BASE_URL; ?>pages/services.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>">Dịch vụ</a></li>
                    <li><a href="<?php echo BASE_URL; ?>pages/about.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">Về chúng tôi</a></li>
                    <li><a href="<?php echo BASE_URL; ?>pages/location.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'location.php' ? 'active' : ''; ?>">Địa chỉ TL Barber</a></li>
                    <?php if ($current_user): ?>
                        <li><a href="<?php echo BASE_URL; ?>pages/booking.php">Đặt lịch</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/booking-history.php">Lịch sử</a></li>
                        <?php if ($current_user['role'] === 'admin'): ?>
                            <li><a href="<?php echo BASE_URL; ?>admin/index.php" style="color: var(--secondary-color);">
                                <i class="fas fa-shield-alt"></i> Admin
                            </a></li>
                        <?php endif; ?>
                        <li class="nav-user">
                            <a href="#" class="user-menu-toggle">
                                <i class="fas fa-user-circle"></i>
                                <?php echo htmlspecialchars($current_user['full_name']); ?>
                                <i class="fas fa-chevron-down"></i>
                            </a>
                            <ul class="user-dropdown">
                                <li><a href="<?php echo BASE_URL; ?>pages/profile.php"><i class="fas fa-user"></i> Hồ sơ</a></li>
                                <li><a href="<?php echo BASE_URL; ?>pages/notifications.php">
                                    <i class="fas fa-bell"></i> Thông báo
                                    <?php
                                    $pdo = getDBConnection();
                                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
                                    $stmt->execute([$current_user['id']]);
                                    $unread = $stmt->fetch()['count'];
                                    if ($unread > 0) {
                                        echo '<span class="badge">' . $unread . '</span>';
                                    }
                                    ?>
                                </a></li>
                                <li><a href="<?php echo BASE_URL; ?>api/logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>pages/login.php">Đăng nhập</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/register.php" class="btn-primary">Đăng ký</a></li>
                    <?php endif; ?>
                </ul>
                <div class="nav-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>
    <main class="main-content">

