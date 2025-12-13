<?php
$page_title = 'Đăng nhập';
require_once __DIR__ . '/../includes/header.php';

if ($current_user) {
    redirect(BASE_URL . 'index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ thông tin';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $token = generateJWT($user);
            setcookie('auth_token', $token, time() + JWT_EXPIRATION, '/');
            
            // Redirect based on user role
            if ($user['role'] === 'admin') {
                redirect(BASE_URL . 'admin/index.php');
            } elseif ($user['role'] === 'barber') {
                redirect(BASE_URL . 'barber/index.php');
            } else {
                redirect(BASE_URL . 'index.php');
            }
        } else {
            $error = 'Email hoặc mật khẩu không đúng';
        }
    }
}
?>

<div class="section">
    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-2"><i class="fas fa-sign-in-alt"></i> Đăng nhập</h2>
            <p class="text-center mb-3" style="color: var(--text-light);">
                Chưa có tài khoản? <a href="<?php echo BASE_URL; ?>pages/register.php" style="color: var(--primary-color);">Đăng ký ngay</a>
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
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" class="form-control" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mật khẩu</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                    <label style="margin: 0;">
                        <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                    </label>
                    <a href="<?php echo BASE_URL; ?>pages/forgot-password.php" style="color: var(--primary-color); text-decoration: none;">
                        Quên mật khẩu?
                    </a>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

