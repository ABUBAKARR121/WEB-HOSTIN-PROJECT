<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    // Log activity
    $log = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (?, 'User logged out', ?)");
    $log->execute([$_SESSION['user_id'], $_SERVER['REMOTE_ADDR']]);
}

session_destroy();
header("Location: login.php");
exit();
?>