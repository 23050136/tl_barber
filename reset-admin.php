<?php
require_once __DIR__ . '/config/config.php';
$pdo = getDBConnection();
$newHash = password_hash('123456', PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin', email = 'admin@tlbarber.com' WHERE email = 'admin@tlbarber.com'");
$stmt->execute([$newHash]);

echo "Done. New hash: $newHash\n";