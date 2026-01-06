<?php
require_once 'db_conn.php';
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
$message = "";

// --- 1. 撈取下拉選單需要的資料 (成員 & 任務) ---

// A. 撈取所有成員 (為了下拉選單)
$members_sql = "SELECT * FROM member";
$members_result = $conn->query($members_sql);

// B. 撈取所有任務種類 (為了下拉選單)
$missions_sql = "SELECT * FROM contribution_table";
$missions_result = $conn->query($missions_sql);
// --- 2. 處理表單送出 ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mission_type = $_POST['Mission_type'];
    $member_id = $_POST['Member_Id']; // 對應下方表單的 name

    try {
        // ★ 步驟 A：根據使用者選的任務，去查對應的點數 (point)
        $point_sql = "SELECT point FROM contribution_table WHERE Mission_type = ?";
        //$missions_show_sql = "SELECT * FROM contribution_record WHERE Member_Id = ?";//
        $stmt_pt = $conn->prepare($point_sql);
        $stmt_pt->bind_param("s", $mission_type);
        $stmt_pt->execute();
        //$stmt_missions_show = $conn->prepare($missions_show_sql);//
        //$stmt_missions_show->bind_param("s", $member_id);//
        //$stmt_missions_show->execute();//
        //$stmt_missions_show_result = $stmt_missions_show->get_result()->fetch_row()[2];
        //$stmt_missions_show_result_result = "SELECT * FROM stmt_missions_show_result, contribution_record WHERE stmt_missions_show_result.Mission_type <> contribution_record.Mission_type";
        $result_pt = $stmt_pt->get_result();
        if ($row_pt = $result_pt->fetch_assoc()) {
            $auto_point = $row_pt['point']; // 抓到了！這是系統設定的標準點數
        } else {
            throw new Exception("找不到該任務的點數設定！");
        }

        // ★ 步驟 B：寫入資料庫
        // 1. 表名改成 contribution_record
        // 2. 欄位改成 Member_Id
        // 3. 移除了 Date
        $sql = "INSERT INTO contribution_record (Mission_type, Member_Id, point) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        // 參數綁定：s (字串), s (字串), i (整數) -> 對應 Mission_type, Member_Id, point
        $stmt->bind_param("ssi", $mission_type, $member_id, $auto_point);

        if ($stmt->execute()) {
            // 成功後跳轉回列表
            header("Location: contribution_circuit.php");
            exit();
        }
    } catch (Exception $e) {
        $message = "❌ 錯誤：" . $e->getMessage();
    } catch (mysqli_sql_exception $e) {
        $message = "❌ 資料庫錯誤：" . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>新增貢獻紀錄</title>
    <style>
        body { font-family: "Microsoft JhengHei"; padding: 20px; background-color: #f5f5f5; }
        .form-container { background: white; padding: 30px; border-radius: 8px; max-width: 500px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-top: 15px; font-weight: bold; color: #555; }
        select, input { width: 100%; margin-top: 5px; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; background-color: #5e4b8b; color: white; border: none; padding: 12px; margin-top: 25px; cursor: pointer; border-radius: 5px; font-size: 16px; }
        button:hover { background-color: #4a3b6e; }
        .error-msg { color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>📝 新增貢獻紀錄</h2>
    
    <?php if (!empty($message)): ?>
        <div class="error-msg"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post" action="">
        
        <!-- 1. 成員下拉選單 -->
        <label>完成人員 (Member ID):</label>
        <select name="Member_Id" required>
            <option value="" disabled selected>-- 請選擇成員 --</option>
            <?php 
            if ($members_result->num_rows > 0) {
                while($m = $members_result->fetch_assoc()) {
                    // 顯示 ID 和 Name
                    $show_name = isset($m['Name']) ? $m['Name'] : ""; 
                    echo "<option value='" . $m['Member_Id'] . "'>" . $m['Member_Id'] . " " . $show_name . "</option>";
                }
            }
            ?>
        </select>
        
        <!-- 2. 任務下拉選單 -->
        <label>任務名稱 (Mission Type):</label>
        <select name="Mission_type" required>
            <option value="" disabled selected>-- 請選擇任務 --</option>
            <?php 
            if ($missions_result->num_rows > 0) {
                while($t = $missions_result->fetch_assoc()) {
                    // 顯示任務名稱與點數
                    echo "<option value='" . $t['Mission_type'] . "'>" . $t['Mission_type'] . " (" . $t['point'] . " 點)</option>";
                }
            }
            ?>
        </select>
        
        <!-- 日期欄位已移除 -->
        
        <button type="submit">確認新增</button>
        <a href="contribution_circuit.php" class="back-link">取消返回</a>
    </form>
</div>

</body>
</html>
