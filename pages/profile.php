<?php
$page_title = 'Hồ sơ cá nhân';
require_once __DIR__ . '/../includes/header.php';

if (!$current_user) {
    redirect(BASE_URL . 'pages/login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    
    if (empty($full_name)) {
        $error = 'Họ và tên không được để trống';
    } else {
        $pdo = getDBConnection();
        
        // Update basic info
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
        $stmt->execute([$full_name, $phone, $current_user['id']]);
        
        // Update password if provided
        if (!empty($new_password)) {
            if (empty($current_password)) {
                $error = 'Vui lòng nhập mật khẩu hiện tại';
            } elseif (!password_verify($current_password, $current_user['password'])) {
                $error = 'Mật khẩu hiện tại không đúng';
            } elseif (strlen($new_password) < 6) {
                $error = 'Mật khẩu mới phải có ít nhất 6 ký tự';
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $current_user['id']]);
                $success = 'Cập nhật thông tin và mật khẩu thành công';
            }
        } else {
            $success = 'Cập nhật thông tin thành công';
        }
        
        // Refresh user data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$current_user['id']]);
        $current_user = $stmt->fetch();
    }
}
?>

<div class="section">
    <div class="container">
        <div class="form-container" style="max-width: 600px;">
            <h2 class="text-center mb-2"><i class="fas fa-user-circle"></i> Hồ sơ cá nhân</h2>
            
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
                    <label for="full_name"><i class="fas fa-user"></i> Họ và tên</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required
                           value="<?php echo htmlspecialchars($current_user['full_name']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" class="form-control" 
                           value="<?php echo htmlspecialchars($current_user['email']); ?>" disabled>
                    <small style="color: var(--text-light);">Email không thể thay đổi</small>
                </div>
                
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Số điện thoại</label>
                    <input type="tel" id="phone" name="phone" class="form-control"
                           value="<?php echo htmlspecialchars($current_user['phone'] ?? ''); ?>">
                </div>
                
                <hr style="margin: 2rem 0; border: 1px solid var(--border-color);">
                
                <h3 style="margin-bottom: 1rem; color: var(--primary-color);">Đổi mật khẩu</h3>
                <p style="color: var(--text-light); margin-bottom: 1rem; font-size: 0.9rem;">
                    Để trống nếu không muốn đổi mật khẩu
                </p>
                
                <div class="form-group">
                    <label for="current_password"><i class="fas fa-lock"></i> Mật khẩu hiện tại</label>
                    <input type="password" id="current_password" name="current_password" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="new_password"><i class="fas fa-lock"></i> Mật khẩu mới</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" minlength="6">
                    <small style="color: var(--text-light);">Tối thiểu 6 ký tự</small>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

