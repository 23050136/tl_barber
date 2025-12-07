<?php
$page_title = 'Đăng ký';
require_once __DIR__ . '/../includes/header.php';

if ($current_user) {
    redirect(BASE_URL . 'index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($full_name) || empty($email) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự';
    } else {
        $pdo = getDBConnection();
        
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email này đã được sử dụng';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$full_name, $email, $phone, $hashed_password])) {
                $success = 'Đăng ký thành công! Đang chuyển đến trang đăng nhập...';
                header("refresh:2;url=" . BASE_URL . "pages/login.php");
            } else {
                $error = 'Có lỗi xảy ra, vui lòng thử lại';
            }
        }
    }
}
?>

<div class="section">
    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-2"><i class="fas fa-user-plus"></i> Đăng ký tài khoản</h2>
            <p class="text-center mb-3" style="color: var(--text-light);">
                Đã có tài khoản? <a href="<?php echo BASE_URL; ?>pages/login.php" style="color: var(--primary-color);">Đăng nhập ngay</a>
            </p>
            
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
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="full_name"><i class="fas fa-user"></i> Họ và tên *</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required
                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email *</label>
                    <input type="email" id="email" name="email" class="form-control" required
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Số điện thoại</label>
                    <input type="tel" id="phone" name="phone" class="form-control"
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mật khẩu *</label>
                    <input type="password" id="password" name="password" class="form-control" required minlength="6">
                    <small style="color: var(--text-light);">Tối thiểu 6 ký tự</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Xác nhận mật khẩu *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-user-plus"></i> Đăng ký
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

