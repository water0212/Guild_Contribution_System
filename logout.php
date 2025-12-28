<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
session_destroy();        // 清除所有 session
echo "<script>alert('登出成功！'); window.location.href='index.php';</script>";
exit();
?>

