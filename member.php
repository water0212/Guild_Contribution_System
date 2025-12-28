<?php
require_once 'db_conn.php';
$s_name = isset($_GET['search_name']) ? $_GET['search_name'] : "";
$s_op   = isset($_GET['point_op']) ? $_GET['point_op'] : "=";
$s_point= isset($_GET['search_point']) ? $_GET['search_point'] : "";
// 2. 執行 SQL 查詢
$sql = "SELECT * FROM member WHERE 1=1";
if (!empty($s_name)) {
    $safe_name = $conn->real_escape_string($s_name);
    $sql .= " AND Name LIKE '%$safe_name%'";
}

if ($s_point !== "") {
    $safe_point = (int)$s_point;
    if (in_array($s_op, ['=', '>', '<', '>=', '<='])) {
        $sql .= " AND Contribution_sum $s_op $safe_point";
    }
}
$result = $conn->query($sql);


?>


<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
            text-align: center;
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

        /* 搜尋區塊樣式 */
        body .search-bar {
            background: white;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        body .search-bar input, body .search-bar select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 5px;
        }

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
        @media (max-width: 776px) {
    td, th {
        padding: 8px;
        font-size: 12px;
    }

    .nav-btn {
        font-size: 12px;
    }

    .card-header h4 {
        font-size: 16px;
    }
    .header h1{
        font-size: 20px;
        width: auto;
        position: absolute;
        right: 5%;
    }
    .header{
        padding: 30px;
    }
}

    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[首頁]公會成員列表</title>
</head>
<body>
    <div class="header">
        <div class="nav-buttons">
            <a href="contribution_circuit.php" class="nav-btn">≡ 貢獻紀錄</a>
            <a href="contribution_table.php" class="nav-btn">≡ 貢獻任務表</a>
            <a href="member.php" class="nav-btn">👥 成員表</a>
        </div>
        <h2>🏰 首頁</h2>
    </div>
    <div class="search-bar">
        <form method="GET" action="">
        <label>人物名稱：</label>
        <input type="text" name="search_name" placeholder="輸入名字" value="<?php echo htmlspecialchars($s_name); ?>">
        
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
            <a href="member.php" style="margin-left: 10px; color: #666; text-decoration: underline;">清除搜尋</a>
        <?php endif; ?>
        </form>
    </div>
<div class="container mt-5">
    
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">🛡️ 公會成員列表</h4>
        </div>
        <div class="card-body">
            <!-- 按鈕區 -->
            <div class="mb-3">
                <a href="member_new.php" class="btn btn-success">+ 新增成員</a>
            </div>

            <!-- 表格區 -->
            <table class="table table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>姓名</th>
                        <th>貢獻度總計</th>
                        <th>管理操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["Member_Id"] . "</td>";
                            echo "<td><strong>" . $row["Name"] . "</strong></td>";
                            
                            $badgeColor = $row["Contribution_sum"] > 50 ? "text-success" : "text-muted";
                            echo "<td class='$badgeColor fw-bold'>" . $row["Contribution_sum"] . " pts</td>";
                            
                            echo "<td>
                                    <a href='member_edit.php?id=" . $row["Member_Id"] . "' class='btn btn-warning btn-sm'>編輯</a>
                                    <a href='member_delete.php?id=" . $row["Member_Id"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"確定要將此人踢出公會嗎？\");'>刪除</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center text-muted'>目前公會沒有成員，請趕快招募！</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
