<?php
// 引入資料庫連線
require_once 'db_conn.php';

// --- 1. 統計區塊 (保持不變) ---
$stat_sql = "SELECT 
                COUNT(*) as total_missions, 
                SUM(point) as total_points, 
                AVG(point) as avg_point 
             FROM contribution_table";
$stat_result = $conn->query($stat_sql);
$stat_row = $stat_result->fetch_assoc();

// --- 2. 搜尋邏輯 (大幅升級) ---

// 初始化搜尋變數 (為了讓 HTML 表單可以記住剛剛輸入的值)
$s_name = isset($_GET['search_name']) ? $_GET['search_name'] : "";
$s_op   = isset($_GET['point_op']) ? $_GET['point_op'] : "=";
$s_point= isset($_GET['search_point']) ? $_GET['search_point'] : "";

// ★ 技巧：使用 WHERE 1=1，後面可以無限串接 AND
$sql = "SELECT * FROM contribution_table WHERE 1=1";

// 條件 A：如果有輸入名稱
if (!empty($s_name)) {
    // 使用 real_escape_string 防止簡單的 SQL Injection
    $safe_name = $conn->real_escape_string($s_name);
    $sql .= " AND Mission_type LIKE '%$safe_name%'";
}

// 條件 B：如果有輸入點數
if ($s_point !== "") {
    $safe_point = (int)$s_point; // 強制轉成數字，安全
    // 檢查運算符號是否合法 (防止被惡意竄改)
    if (in_array($s_op, ['=', '>', '<', '>=', '<='])) {
        $sql .= " AND point $s_op $safe_point";
    }
}

// 執行最終組裝好的 SQL
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>可完成的貢獻任務表</title>
    <!-- 引入 SweetAlert2 (雖然暫時不用，但先留著) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            justify-content: center;
            position: relative;
        }

        .nav-buttons {
            position: absolute;
            left: 20px;
            display: flex;
            gap: 10px;
        }

        .nav-btn {
            background-color: #5e4b8b;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 20px;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        /* 統計區塊樣式 */
        .stat-bar {
            background: #fff3cd;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
            font-size: 0.9em;
        }

        /* 搜尋區塊樣式 */
        .search-bar {
            background: white;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .search-bar input, .search-bar select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 5px;
        }

        /* 表格容器 */
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 20px auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
            padding: 20px;
        }

        /* 表格樣式 */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #3d3d3d; /* 深色表頭 */
            color: white;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9; /* 斑馬紋 */
        }

        /* 按鈕樣式 */
        .btn-edit { color: #5e4b8b; text-decoration: none; font-weight: bold; margin-right: 10px; }
        .btn-delete { color: #d9534f; text-decoration: none; font-weight: bold; }
        
        .action-bar { margin-bottom: 15px; }
        .add-btn {
            display: inline-block;
            border: 1px solid #ccc;
            padding: 5px 15px;
            border-radius: 20px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
        }
    </style>
</head>
<body>

<!-- 導覽列 -->
<div class="header">
    <div class="nav-buttons">
        <a href="#" class="nav-btn">≡ 貢獻紀錄</a>
        <a href="contribution_table.php" class="nav-btn">≡ 貢獻任務表</a>
        <a href="#" class="nav-btn">≡ 成員表</a>
    </div>
    <h2>公會名稱</h2>
</div>

<!-- 統計資訊 -->
<div class="stat-bar">
    📊 任務統計：目前共有 <b><?php echo $stat_row['total_missions']; ?></b> 個任務，
    總貢獻點數 <b><?php echo $stat_row['total_points']; ?></b> 點，
    平均每個任務 <b><?php echo number_format($stat_row['avg_point'], 1); ?></b> 點。
</div>

<!-- ★ 搜尋區塊 (新增點數搜尋) -->
<div class="search-bar">
    <form method="GET" action="">
        <label>任務名稱：</label>
        <input type="text" name="search_name" placeholder="輸入關鍵字..." value="<?php echo htmlspecialchars($s_name); ?>">
        
        <label style="margin-left: 15px;">點數：</label>
        <select name="point_op">
            <option value="=" <?php if($s_op == '=') echo 'selected'; ?>>等於 (=)</option>
            <option value=">" <?php if($s_op == '>') echo 'selected'; ?>>大於 (>)</option>
            <option value="<" <?php if($s_op == '<') echo 'selected'; ?>>小於 (<)</option>
            <option value=">=" <?php if($s_op == '>=') echo 'selected'; ?>>大於等於 (>=)</option>
            <option value="<=" <?php if($s_op == '<=') echo 'selected'; ?>>小於等於 (<=)</option>
        </select>
        <input type="number" name="search_point" placeholder="輸入點數" value="<?php echo htmlspecialchars($s_point); ?>" style="width: 80px;">
        
        <button type="submit" class="nav-btn">🔍 搜尋</button>
        
        <?php if(!empty($s_name) || $s_point !== ""): ?>
            <a href="contribution_table.php" style="margin-left: 10px; color: #666; text-decoration: underline;">清除搜尋</a>
        <?php endif; ?>
    </form>
</div>

<div class="container">
    <h3 style="text-align: center;">可完成的貢獻任務表</h3>
    
    <div class="action-bar">
        <a href="contribution_table_add.php" class="add-btn">＋ 新增</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>任務種類 (Mission_type)</th>
                <th>任務敘述 (Text)</th>
                <th>點數 (point)</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["Mission_type"] . "</td>";
                    echo "<td>" . $row["Text"] . "</td>";
                    echo "<td>" . $row["point"] . "</td>";
                    echo "<td>";
                    echo "<a href='contribution_table_edit.php?id=" . $row["Mission_type"] . "' class='btn-edit'>修改</a> ";
                    // 這裡先保留原本的 onclick confirm，之後再改 SweetAlert
                    echo "<a href='contribution_table_delete.php?id=" . $row["Mission_type"] . "' onclick='return confirm(\"確定要刪除嗎？\");' class='btn-delete'>刪除</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4' style='text-align:center; color: #888;'>查無資料</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
