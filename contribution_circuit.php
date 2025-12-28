<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
// 引入資料庫連線
require_once 'db_conn.php';
// --- 2. 搜尋邏輯 (大幅升級) ---

// 初始化搜尋變數 (為了讓 HTML 表單可以記住剛剛輸入的值)
$s_mission = isset($_GET['search_mission']) ? $_GET['search_mission'] : "";
$s_member = isset($_GET['search_member']) ? $_GET['search_member'] : "";



// ★ 技巧：使用 WHERE 1=1，後面可以無限串接 AND
$sql = "SELECT * FROM contribution_record inner join member ON contribution_record.Member_Id = member.Member_Id WHERE 1=1";

// 條件 A：如果有輸入任務名稱
if (!empty($s_mission)) {
    // 使用 real_escape_string 防止簡單的 SQL Injection
    $safe_mission = $conn->real_escape_string($s_mission);
    $sql .= " AND Mission_type LIKE '%$safe_mission%'";
}

// 條件 B：如果有輸入成員名稱
if (!empty($s_member)) {
    $safe_member = $conn->real_escape_string($s_member);
    $sql .= " AND Name LIKE '%$safe_member%'";
}

// 執行最終組裝好的 SQL
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>貢獻任務紀錄表</title>
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
        <a href="contribution_circuit.php" class="nav-btn">≡ 貢獻紀錄</a>
        <a href="contribution_table.php" class="nav-btn">≡ 貢獻任務表</a>
        <a href="member.php" class="nav-btn">👥 成員表</a>
    </div>
    <h2>公會名稱</h2>
    <div class="nav-buttons" style="right: 20px; left: auto;">
        <a href="logout.php" class="nav-btn">🚪 登出</a>
    </div>
</div>

<!-- ★ 搜尋區塊 (新增點數搜尋) -->
<div class="search-bar">
    <form method="GET" action="">
        <label>任務名稱：</label>
        <input type="text" name="search_mission" placeholder="輸入關鍵字..." value="<?php echo htmlspecialchars($s_mission); ?>">
        
        <label style="margin-left: 15px;">成員：</label>
        <input type="text" name="search_member" placeholder="輸入成員名稱" value="<?php echo htmlspecialchars($s_member); ?>" style="width: 150px;">
        
        <button type="submit" class="nav-btn">🔍 搜尋</button>
        
        <?php if(!empty($s_mission) || !empty($s_member)): ?>
            <a href="contribution_circuit.php" style="margin-left: 10px; color: #666; text-decoration: underline;">清除搜尋</a>
        <?php endif; ?>
    </form>
</div>

<div class="container">
    <h3 style="text-align: center;">貢獻任務紀錄表</h3>
    
    <div class="action-bar">
        <?php
        if ($_SESSION['username'] <> "guest"){
           echo '<a href="contribution_circuit_add.php" class="add-btn">＋ 新增</a>';
        } 
        ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>紀錄編號 (record_id)</th>
                <th>任務種類 (Mission_type)</th>
                <th>完成成員 (Name)</th>
                <th>點數 (point)</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["record_id"] . "</td>";
                    echo "<td>" . $row["Mission_type"] . "</td>";
                    echo "<td>" . $row["Name"] . "</td>";
                    echo "<td>" . $row["point"] . "</td>";
                    echo "<td>";
                    if($_SESSION['username'] <> "guest"){
                        echo "<a href='contribution_circuit_edit.php?id=" . $row["Mission_type"] . "' class='btn-edit'>修改</a> ";
                        // 這裡先保留原本的 onclick confirm，之後再改 SweetAlert
                        echo "<a href='contribution_circuit_delete.php?id=" . $row["record_id"] . "' onclick='return confirm(\"確定要刪除嗎？\");' class='btn-delete'>刪除</a>";
                    }
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
