<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
require_once "db_conn.php";

$id = intval($_GET['id']);
$s = "SELECT * FROM member WHERE Member_Id = $id";
$r = $conn->query($s);
if($r<>$_SESSION['username']){
    echo "<script>alert('無法刪除自己！'); window.location.href='member.php';</script>";
    exit;
}
else{
$sql = "DELETE  FROM member
        WHERE Member_Id = $id";

if ($conn->query($sql) === TRUE) {
        echo "<script>alert('刪除成功！'); window.location.href='member.php';</script>";
        exit;
} else {
    echo "更新失敗：" . $conn->error;
}
}


