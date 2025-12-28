<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
require_once "db_conn.php";
$name = $_POST['Name'];

$sql = "INSERT INTO member
        VALUES (NULL, '$name', 0,'非活躍狀態',NULL)";
if($name==""||$name=="guest"){
    echo "<script>alert('無法這樣命名'); window.location.href='member_new.php';</script>";
    exit;
}
if ($conn->query($sql) === TRUE) {
    echo "<script>alert('新增成功！'); window.location.href='member.php';</script>";
    exit;
} else {
    echo "更新失敗：" . $conn->error;
}
