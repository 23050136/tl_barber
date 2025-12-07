<?php
$page_title = 'Gán dịch vụ cho barber';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$admin = checkAdminAuth();
$pdo = getDBConnection();
$error = '';
$success = '';

$barber_id = $_GET['barber_id'] ?? 0;

if (!$barber_id) {
    redirect(BASE_URL . 'admin/barbers.php');
}

// Get barber info
$stmt = $pdo->prepare("
    SELECT b.*, u.full_name
    FROM barbers b
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ?
");
$stmt->execute([$barber_id]);
$barber = $stmt->fetch();

if (!$barber) {
    redirect(BASE_URL . 'admin/barbers.php');
}

// Handle assign/remove service
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = intval($_POST['service_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    
    if ($service_id && $action) {
        if ($action === 'assign') {
            // Check if already assigned
            $stmt = $pdo->prepare("SELECT id FROM service_barber WHERE service_id = ? AND barber_id = ?");
            $stmt->execute([$service_id, $barber_id]);
            if ($stmt->fetch()) {
                $error = 'Dịch vụ này đã được gán cho barber';
            } else {
                $stmt = $pdo->prepare("INSERT INTO service_barber (service_id, barber_id) VALUES (?, ?)");
                if ($stmt->execute([$service_id, $barber_id])) {
                    $success = 'Gán dịch vụ thành công';
                } else {
                    $error = 'Có lỗi xảy ra';
                }
            }
        } elseif ($action === 'remove') {
            $stmt = $pdo->prepare("DELETE FROM service_barber WHERE service_id = ? AND barber_id = ?");
            if ($stmt->execute([$service_id, $barber_id])) {
                $success = 'Gỡ dịch vụ thành công';
            } else {
                $error = 'Có lỗi xảy ra';
            }
        }
    }
}

// Get all services
$stmt = $pdo->query("SELECT * FROM services ORDER BY name");
$all_services = $stmt->fetchAll();

// Get assigned services
$stmt = $pdo->prepare("
    SELECT s.*
    FROM services s
    JOIN service_barber sb ON s.id = sb.service_id
    WHERE sb.barber_id = ?
    ORDER BY s.name
");
$stmt->execute([$barber_id]);
$assigned_services = $stmt->fetchAll();
$assigned_ids = array_column($assigned_services, 'id');
?>

<div class="section">
    <div class="container">
        <div style="margin-bottom: 2rem;">
            <a href="<?php echo BASE_URL; ?>admin/barbers.php" style="color: var(--primary-color); text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách barber
            </a>
        </div>
        
        <h1 style="color: var(--primary-color); margin-bottom: 2rem;">
            <i class="fas fa-cut"></i> Gán dịch vụ cho: <?php echo htmlspecialchars($barber['full_name']); ?>
        </h1>
        
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
        
        <!-- Assigned Services -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-body">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Dịch vụ đã gán</h2>
                <?php if (empty($assigned_services)): ?>
                    <p style="color: var(--text-light); text-align: center; padding: 2rem;">
                        Chưa có dịch vụ nào được gán
                    </p>
                <?php else: ?>
                    <div class="services-grid">
                        <?php foreach ($assigned_services as $service): ?>
                            <div class="card">
                                <div class="card-body">
                                    <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                                        <?php echo htmlspecialchars($service['name']); ?>
                                    </h3>
                                    <p style="color: var(--text-light); margin-bottom: 0.5rem;">
                                        <?php echo formatPrice($service['price']); ?> - <?php echo $service['duration']; ?> phút
                                    </p>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                        <input type="hidden" name="action" value="remove">
                                        <button type="submit" class="btn btn-danger" style="font-size: 0.9rem; padding: 5px 15px;">
                                            <i class="fas fa-times"></i> Gỡ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Available Services -->
        <div class="card">
            <div class="card-body">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Tất cả dịch vụ</h2>
                <div class="services-grid">
                    <?php foreach ($all_services as $service): ?>
                        <div class="card">
                            <div class="card-body">
                                <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                                    <?php echo htmlspecialchars($service['name']); ?>
                                </h3>
                                <p style="color: var(--text-light); margin-bottom: 0.5rem;">
                                    <?php echo formatPrice($service['price']); ?> - <?php echo $service['duration']; ?> phút
                                </p>
                                <?php if (in_array($service['id'], $assigned_ids)): ?>
                                    <span style="color: var(--success-color);">
                                        <i class="fas fa-check"></i> Đã gán
                                    </span>
                                <?php else: ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                        <input type="hidden" name="action" value="assign">
                                        <button type="submit" class="btn btn-primary" style="font-size: 0.9rem; padding: 5px 15px;">
                                            <i class="fas fa-plus"></i> Gán
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

