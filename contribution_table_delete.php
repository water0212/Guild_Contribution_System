<?php
require_once 'db_conn.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 執行刪除
    $sql = "DELETE FROM contribution_table WHERE Mission_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        // 刪除成功，跳回列表
        header("Location: contribution_table.php");
    } else {
        echo "刪除失敗：" . $conn->error;
    }
}
?>
