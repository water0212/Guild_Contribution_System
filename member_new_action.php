<?php
require_once "db_conn.php";
$name = $_POST['Name'];

$sql = "INSERT INTO member
        VALUES (NULL, '$name', 0)";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('新增成功！'); window.location.href='member.php';</script>";
    exit;
} else {
    echo "更新失敗：" . $conn->error;
}
