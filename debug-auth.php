<?php
require_once __DIR__ . '/config/config.php';

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT id, full_name, email, password, role FROM users WHERE email = 'admin@tlbarber.com'");
$stmt->execute();
$user = $stmt->fetch();

header('Content-Type: text/plain; charset=utf-8');

echo "User record:\n";
var_dump($user);

echo "\npassword length: " . (isset($user['password']) ? strlen($user['password']) : 'null') . "\n";
echo "password starts with \$2y$? ";
echo (isset($user['password']) && strpos($user['password'], '$2y$') === 0) ? 'yes' : 'no';
echo "\n\npassword_verify('123456'): ";
var_dump(password_verify('123456', $user['password'] ?? ''));

if ($user) {
    if (password_verify('123456', $user['password'])) {
        echo "\n\nOK: verify=true, mật khẩu đã đúng 123456.\n";
    } else {
        echo "\n\nERROR: verify=false, hash trong DB không khớp 123456.\n";
        echo "Thử cập nhật lại hash theo bước dưới.\n";
    }
} else {
    echo "\n\nERROR: Không tìm thấy user admin@tlbarber.com\n";
}

