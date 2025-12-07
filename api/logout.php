<?php
require_once __DIR__ . '/../config/config.php';

setcookie('auth_token', '', time() - 3600, '/');
session_destroy();
redirect(BASE_URL . 'index.php');
?>

