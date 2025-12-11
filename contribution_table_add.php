<?php
require_once 'db_conn.php';

$message = ""; // 用來顯示錯誤或成功訊息

// 檢查是否有表單送出
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ★ 變更：接收表單資料的名稱改成跟資料庫欄位一致
    $name = $_POST['Mission_type'];
    $desc = $_POST['Text'];
    $points = $_POST['point'];

    try {
        // ★ 變更：SQL 欄位名稱修正
        $sql = "INSERT INTO contribution_table (Mission_type, Text, point) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $desc, $points); // s=string, i=integer

        if ($stmt->execute()) {
            // 成功後跳轉回列表頁
            header("Location: contribution_table.php");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        // 捕捉錯誤 (例如主鍵重複)
        if ($e->getCode() == 1062) { 
            $message = "❌ 錯誤：任務名稱「" . $name . "」已經存在，請勿重複新增！";
        } else {
            $message = "❌ 資料庫錯誤：" . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>新增任務</title>
    <style>
        body { font-family: "Microsoft JhengHei"; padding: 20px; background-color: #f5f5f5; }
        .form-container { background: white; padding: 20px; border-radius: 8px; max-width: 500px; margin: 0 auto; }
        input, textarea { width: 100%; margin-bottom: 10px; padding: 8px; }
        button { background-color: #5e4b8b; color: white; border: none; padding: 10px; cursor: pointer; border-radius: 5px; }
        .error-msg { color: red; font-weight: bold; background: #ffe6e6; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>新增貢獻任務</h2>
    
    <?php if (!empty($message)): ?>
        <div class="error-msg"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label>任務名稱 (主鍵):</label>
        <!-- ★ 變更：name 屬性改成 Mission_type -->
        <input type="text" name="Mission_type" required>
        
        <label>任務敘述:</label>
        <!-- ★ 變更：name 屬性改成 Text -->
        <textarea name="Text" required></textarea>
        
        <label>貢獻點數:</label>
        <!-- ★ 變更：name 屬性改成 point -->
        <input type="number" name="point" required>
        
        <button type="submit">確認新增</button>
        <a href="contribution_table.php" style="margin-left:10px;">取消</a>
    </form>
</div>

</body>
</html>
