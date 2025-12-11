<?php
require_once "db_conn.php";

$id = intval($_GET['id']);

$sql = "DELETE  FROM member
        WHERE Member_Id = $id";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('刪除成功！'); window.location.href='member.php';</script>";
    exit;
} else {
    echo "更新失敗：" . $conn->error;
}
