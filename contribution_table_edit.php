<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
require_once 'db_conn.php';

// 1. 取得要修改的任務 ID (Primary Key)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // ★ 變更：WHERE 條件改成 Mission_type
    $sql = "SELECT * FROM contribution_table WHERE Mission_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
}

// 2. 處理更新動作
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $original_id = $_POST['original_id']; // 舊的主鍵
    
    // ★ 變更：接收變數名稱
    $desc = $_POST['Text'];
    $points = $_POST['point'];
    
    // ★ 變更：SQL 更新語法 (SET Text, point WHERE Mission_type)
    $sql = "UPDATE contribution_table SET Text = ?, point = ? WHERE Mission_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $desc, $points, $original_id);

    if ($stmt->execute()) {
        header("Location: contribution_table.php");
        exit();
    } else {
        echo "更新失敗: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>修改任務</title>
    <style>
        body { font-family: "Microsoft JhengHei"; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; max-width: 500px; margin: auto; border-radius: 8px; }
        input, textarea { width: 100%; margin: 5px 0 15px; padding: 8px; }
    </style>
</head>
<body>
<div class="container">
    <!-- ★ 變更：顯示變數改成 $row['Mission_type'] -->
    <h2>修改任務：<?php echo $row['Mission_type']; ?></h2>
    
    <form method="post">
        <!-- 藏一個隱藏欄位傳送 ID -->
        <input type="hidden" name="original_id" value="<?php echo $row['Mission_type']; ?>">
        
        <label>任務敘述</label>
        <!-- ★ 變更：name 改成 Text，值改成 $row['Text'] -->
        <textarea name="Text"><?php echo $row['Text']; ?></textarea>
        
        <label>點數</label>
        <!-- ★ 變更：name 改成 point，值改成 $row['point'] -->
        <input type="number" name="point" value="<?php echo $row['point']; ?>">
        
        <button type="submit" style="background:#5e4b8b; color:white; border:none; padding:10px; border-radius:5px;">儲存修改</button>
        <a href="contribution_table.php" style="margin-left:10px;">取消</a>
    </form>
</div>
</body>
</html>
