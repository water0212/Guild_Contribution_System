<?php
require_once 'db_conn.php';

// 2. 執行 SQL 查詢
$sql = "SELECT * FROM member";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/member.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[首頁]公會成員列表</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="header">
        <div class="nav-buttons">
            <a href="#" class="nav-btn">≡ 貢獻紀錄</a>
            <a href="contribution_table.php" class="nav-btn">≡ 貢獻任務表</a>
            <a href="member.php" class="nav-btn">👥 成員表</a>
        </div>
        <h2>🏰 首頁</h2>
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
