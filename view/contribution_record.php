<?php
// 引入資料庫連線 (請確保此檔案存在且連線正常)
require_once '../db_conn.php';
require_once '../Components/Header.php';
// http://localhost/Guild_Contribution_System/contribution_record.php
// SQL 查詢：撈取所有任務資料
$sql = "SELECT * FROM contribution_record";
$result = $conn->query($sql);

$header = new Header(['title' => '公會名稱']);
echo $header->render();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>可完成的貢獻任務表</title>
    <link rel="stylesheet" href="../css/contribution.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container">
        <!-- 功能按鈕 -->
        <div class="action-bar">
            <!-- 依照 PDF 檔案結構連結到對應的 PHP -->
            <a href="contribution_table_add.php" class="action-btn">＋ 新增</a>
            <!-- <a href="contribution_table_edit.php" class="action-btn">✎ 修改</a>
            <a href="contribution_table_delete.php" class="action-btn">🗑 刪除</a> -->
        </div>

        <!-- 表格內容 -->
        <table>
            <thead>
                <tr>
                    <th width="20%">任務種類</th>
                    <th width="40%">任務種類</th>
                    <th width="20%">完成人員</th>
                    <th width="20%">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // 輸出每一行資料
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["Mission_type"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["Member_id"]) . "</td>";
                        echo "<td>" . "<a href='contribution_table_edit.php?id=" . $row["Mission_type"] . "' class='btn-edit'>修改</a> ";
                    // 刪除前加入確認視窗
                    echo "<a href='contribution_table_delete.php?id=" . $row["Mission_type"] . "' onclick='return confirm(\"確定要刪除嗎？\");' class='btn-delete'>刪除</a>";
                    echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>目前沒有任務紀錄</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <h2 class="table-title">可完成的貢獻任務表</h2>
    </div>

</body>
</html>
