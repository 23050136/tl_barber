<?php
$page_title = 'Thêm/Sửa barber';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$admin = checkAdminAuth();
$pdo = getDBConnection();
$error = '';
$success = '';

$barber_id = $_GET['id'] ?? 0;
$barber = null;
$user = null;

if ($barber_id) {
    $stmt = $pdo->prepare("
        SELECT b.*, u.full_name, u.email, u.phone
        FROM barbers b
        JOIN users u ON b.user_id = u.id
        WHERE b.id = ?
    ");
    $stmt->execute([$barber_id]);
    $barber = $stmt->fetch();
    if ($barber) {
        $user = $barber;
    } else {
        redirect(BASE_URL . 'admin/barbers.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $specialization = sanitize($_POST['specialization'] ?? '');
    $experience_years = intval($_POST['experience_years'] ?? 0);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    $password = $_POST['password'] ?? '';
    
    if (empty($full_name) || empty($email)) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc';
    } else {
        if ($barber_id && $barber) {
            // Update user
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
            $stmt->execute([$full_name, $phone, $barber['user_id']]);
            
            // Update password if provided
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $barber['user_id']]);
            }
            
            // Update barber
            $stmt = $pdo->prepare("
                UPDATE barbers 
                SET specialization = ?, experience_years = ?, is_available = ?
                WHERE id = ?
            ");
            if ($stmt->execute([$specialization, $experience_years, $is_available, $barber_id])) {
                $success = 'Cập nhật barber thành công';
                redirect(BASE_URL . 'admin/barbers.php');
            } else {
                $error = 'Có lỗi xảy ra';
            }
        } else {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email này đã được sử dụng';
            } else {
                if (empty($password)) {
                    $error = 'Vui lòng nhập mật khẩu cho tài khoản mới';
                } else {
                    // Create user
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, 'barber')");
                    if ($stmt->execute([$full_name, $email, $phone, $hashed_password])) {
                        $user_id = $pdo->lastInsertId();
                        
                        // Create barber
                        $stmt = $pdo->prepare("
                            INSERT INTO barbers (user_id, specialization, experience_years, is_available)
                            VALUES (?, ?, ?, ?)
                        ");
                        if ($stmt->execute([$user_id, $specialization, $experience_years, $is_available])) {
                            $success = 'Thêm barber thành công';
                            redirect(BASE_URL . 'admin/barbers.php');
                        } else {
                            $error = 'Có lỗi xảy ra khi tạo barber';
                        }
                    } else {
                        $error = 'Có lỗi xảy ra khi tạo tài khoản';
                    }
                }
            }
        }
    }
}
?>

<div class="section">
    <div class="container">
        <div class="form-container" style="max-width: 700px;">
            <h2 class="text-center mb-2">
                <i class="fas fa-<?php echo $barber ? 'edit' : 'plus'; ?>"></i> 
                <?php echo $barber ? 'Sửa barber' : 'Thêm barber mới'; ?>
            </h2>
            
            <div style="text-align: center; margin-bottom: 1rem;">
                <a href="<?php echo BASE_URL; ?>admin/barbers.php" style="color: var(--primary-color); text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
            
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
                           value="<?php echo htmlspecialchars($user['full_name'] ?? $_POST['full_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email *</label>
                    <input type="email" id="email" name="email" class="form-control" required
                           value="<?php echo htmlspecialchars($user['email'] ?? $_POST['email'] ?? ''); ?>"
                           <?php echo $barber ? 'readonly' : ''; ?>>
                    <?php if ($barber): ?>
                        <small style="color: var(--text-light);">Email không thể thay đổi</small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Số điện thoại</label>
                    <input type="tel" id="phone" name="phone" class="form-control"
                           value="<?php echo htmlspecialchars($user['phone'] ?? $_POST['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Mật khẩu <?php echo $barber ? '(để trống nếu không đổi)' : '*'; ?></label>
                    <input type="password" id="password" name="password" class="form-control" 
                           <?php echo $barber ? '' : 'required'; ?> minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="specialization"><i class="fas fa-star"></i> Chuyên môn</label>
                    <input type="text" id="specialization" name="specialization" class="form-control"
                           placeholder="VD: Cắt tóc nam, Fade, Tạo kiểu..."
                           value="<?php echo htmlspecialchars($barber['specialization'] ?? $_POST['specialization'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="experience_years"><i class="fas fa-briefcase"></i> Số năm kinh nghiệm</label>
                    <input type="number" id="experience_years" name="experience_years" class="form-control" min="0"
                           value="<?php echo $barber['experience_years'] ?? $_POST['experience_years'] ?? 0; ?>">
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_available" value="1" 
                               <?php echo ($barber['is_available'] ?? true) ? 'checked' : ''; ?>>
                        <span>Đang hoạt động</span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> <?php echo $barber ? 'Cập nhật' : 'Thêm mới'; ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

