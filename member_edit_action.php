<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
require_once "db_conn.php";

$id = intval($_POST['Member_Id']);
$name = $_POST['Name'];
$points = intval($_POST['Contribution']);

$sql = "UPDATE member
        SET Name = '$name', Contribution_sum = $points
        WHERE Member_Id = $id";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('更改成功！'); window.location.href='member.php';</script>";
    exit;
} else {
    echo "更新失敗：" . $conn->error;
}
