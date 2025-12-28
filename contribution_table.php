<?php
// 引入資料庫連線
require_once 'db_conn.php';
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// --- 1. 統計區塊 (為了畫圖表，我們多撈一點資料) ---
// 計算各個等級的任務數量
$chart_sql = "SELECT Mission_type, COUNT(*) as count FROM contribution_table GROUP BY Mission_type";
$chart_res = $conn->query($chart_sql);
$labels = [];
$data = [];
while($row = $chart_res->fetch_assoc()) {
    $labels[] = $row['Mission_type']; // 例如: S級任務
    $data[] = $row['count'];          // 例如: 5
}

// 原本的總分統計
$stat_sql = "SELECT COUNT(*) as total_missions, SUM(point) as total_points FROM contribution_table";
$stat_result = $conn->query($stat_sql);
$stat_row = $stat_result->fetch_assoc();

// --- 2. 列表查詢 ---
$sql = "SELECT * FROM contribution_table ORDER BY point DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>貢獻任務表 (圖表版)</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery (DataTables 需要) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables CSS & JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Chart.js 圖表庫 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: "Microsoft JhengHei", sans-serif; background-color: #f8f9fa; }
        .header { background: linear-gradient(135deg, #1a1a1a, #2c3e50); color: white; padding: 20px; text-align: center; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .nav-btn { background-color: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.5); padding: 8px 15px; border-radius: 20px; text-decoration: none; margin: 0 5px; transition: 0.3s; }
        .nav-btn:hover { background-color: white; color: #1a1a1a; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .stat-card { background: white; padding: 20px; border-radius: 15px; text-align: center; height: 100%; }
        .chart-container { position: relative; height: 250px; width: 100%; display: flex; justify-content: center; }
    </style>
</head>
<body>

<div class="header">
    <div class="mb-3">
        <a href="member.php" class="nav-btn">👥 成員列表</a>
        <a href="contribution_circuit.php" class="nav-btn">≡ 貢獻紀錄</a>
        <a href="logout.php" class="nav-btn bg-danger border-danger">登出</a>
    </div>
    <h2>📜 公會任務佈告欄</h2>
</div>

<div class="container">
    <!-- 上半部：統計圖表區 (新功能!) -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card shadow-sm">
                <h5 class="text-muted">任務總數</h5>
                <h1 class="text-primary fw-bold"><?php echo $stat_row['total_missions']; ?></h1>
                <p>個可執行的任務</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card shadow-sm">
                <h5 class="text-muted">總積分池</h5>
                <h1 class="text-success fw-bold"><?php echo $stat_row['total_points']; ?></h1>
                <p>Points</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card shadow-sm">
                <h5 class="text-muted">任務等級分佈</h5>
                <div class="chart-container">
                    <canvas id="missionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 下半部：資料表格 (DataTables 增強版) -->
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">📋 任務列表</h4>
            <a href="contribution_table_add.php" class="btn btn-primary">+ 新增任務</a>
        </div>
        
        <table id="missionTable" class="table table-hover table-striped" style="width:100%">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>任務類型</th>
                    <th>獲得積分</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["Mission_type"] . "</td>";
                        
                        // 根據等級給不同顏色的標籤
                        $badge = "bg-secondary";
                        if(strpos($row["Mission_type"], 'S') !== false) $badge = "bg-danger";
                        elseif(strpos($row["Mission_type"], 'A') !== false) $badge = "bg-warning text-dark";
                        elseif(strpos($row["Mission_type"], 'B') !== false) $badge = "bg-primary";
                        
                        echo "<td><span class='badge $badge'>" . $row["Mission_type"] . "</span></td>";
                        echo "<td class='fw-bold text-success'>" . $row["point"] . " pts</td>";
                        echo "<td>
                                <a href='contribution_table_edit.php?id=" . $row["Mission_type"] . "' class='btn btn-sm btn-outline-primary'>編輯</a>
                                <a href='contribution_table_delete.php?id=" . $row["Mission_type"] . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"確定刪除嗎？\");'>刪除</a>
                              </td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // 1. 初始化 DataTables (讓表格變高級)
    $(document).ready(function() {
        $('#missionTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/zh-HANT.json" // 繁體中文介面
            },
            "order": [[ 2, "desc" ]] // 預設依積分(第3欄)降序排列
        });
    });

    // 2. 初始化 Chart.js (畫圓餅圖)
    const ctx = document.getElementById('missionChart');
    new Chart(ctx, {
        type: 'doughnut', // 甜甜圈圖
        data: {
            labels: <?php echo json_encode($labels); ?>, // PHP 陣列轉 JS
            datasets: [{
                data: <?php echo json_encode($data); ?>,
                backgroundColor: [
                    '#ff6384', '#36a2eb', '#ffcd56', '#4bc0c0', '#9966ff'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>

</body>
</html>
