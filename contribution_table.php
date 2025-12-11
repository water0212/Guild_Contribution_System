<?php
// 引入資料庫連線 (請確保此檔案存在且連線正常)
require_once 'db_conn.php';
// http://localhost/Guild_Contribution_System/contribution_table.php
// SQL 查詢：撈取所有任務資料
$sql = "SELECT * FROM contribution_table";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>可完成的貢獻任務表</title>
    <style>
        /* 簡單的 CSS 重現圖片風格 */
        body {
            font-family: "Microsoft JhengHei", Arial, sans-serif;
            margin: 0;
            background-color: #f5f5f5;
        }
        
        /* 上方黑色導覽列 */
        .header {
            background-color: #1a1a1a;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: center; /* 標題置中 */
            position: relative;
        }

        .nav-buttons {
            position: absolute;
            left: 20px;
            display: flex;
            gap: 10px;
        }

        .nav-btn {
            background-color: #5e4b8b; /* 紫色按鈕 */
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        h1 { margin: 0; font-size: 24px; }

        /* 主要內容區塊 */
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* 操作按鈕區 (新增/修改/刪除) */
        .action-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
        }

        .action-btn {
            border: 1px solid #ccc;
            background: white;
            padding: 8px 25px;
            border-radius: 20px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            transition: 0.3s;
        }

        .action-btn:hover { background-color: #f0f0f0; }

        /* 表格樣式 */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #4a3b3b; /* 深咖啡色表頭 */
            color: white;
            padding: 12px;
            text-align: center; /* 圖片中看起來是置中 */
            font-weight: normal;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
            color: #333;
        }

        /* 偶數行背景色 (斑馬紋) */
        tr:nth-child(even) {
            background-color: #e0e0e0; 
        }
        
        /* 第一欄 (任務種類) 特別樣式 */
        td:first-child {
            font-weight: bold;
            background-color: #e8e6e6; /* 稍微深一點的灰 */
        }
        
        /* 標題置中 */
        .table-title {
            text-align: center;
            font-size: 28px;
            margin-top: 30px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- 頂部導覽列 -->
    <div class="header">
        <div class="nav-buttons">
            <a href="contribution_circuit.php" class="nav-btn">≡ 貢獻紀錄</a>
            <a href="contribution_table.php" class="nav-btn">≡ 貢獻任務表</a>
            <a href="member.php" class="nav-btn">👥 成員表</a>
        </div>
        <h1>公會名稱</h1>
    </div>

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
                    <th width="40%">任務敘述</th>
                    <th width="20%">點數</th>
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
                        echo "<td>" . htmlspecialchars($row["Text"]) . "</td>";
                        echo "<td>" . htmlspecialchars(string: $row["point"]) . "</td>";
                        echo "<td>" . "<a href='contribution_table_edit.php?id=" . $row["Mission_type"] . "' class='btn-edit'>修改</a> ";
                    // 刪除前加入確認視窗
                    echo "<a href='contribution_table_delete.php?id=" . $row["Mission_type"] . "' onclick='return confirm(\"確定要刪除嗎？\");' class='btn-delete'>刪除</a>";
                    echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>目前沒有任務資料</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <h2 class="table-title">可完成的貢獻任務表</h2>
    </div>

</body>
</html>
