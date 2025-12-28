<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
require_once 'db_conn.php';

// --- 搜尋邏輯 ---
$s_mission = isset($_GET['search_mission']) ? $_GET['search_mission'] : "";
$s_name    = isset($_GET['search_name']) ? $_GET['search_name'] : "";

$sql = "SELECT r.*, m.Name AS MemberName 
        FROM contribution_record r
        LEFT JOIN member m ON r.Member_Id = m.Member_Id 
        WHERE 1=1";

if (!empty($s_mission)) {
    $safe_mission = $conn->real_escape_string($s_mission);
    $sql .= " AND r.Mission_type LIKE '%$safe_mission%'";
}
if (!empty($s_name)) {
    $safe_name = $conn->real_escape_string($s_name);
    $sql .= " AND m.Name LIKE '%$safe_name%'";
}
$sql .= " ORDER BY r.record_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>貢獻紀錄列表</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- 引入 SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .header-bar {
            background-color: #212529; /* 深黑背景 */
            padding: 15px 0;           /* 上下留白 */
            display: flex;             /* 啟用彈性盒模型 */
            justify-content: center;   /* 關鍵：水平置中 */
            align-items: center;       /* 垂直置中 */
            gap: 15px;                 /* 按鈕之間的間距 */
            box-shadow: 0 4px 6px rgba(0,0,0,0.2); /* 陰影 */
        }

        /* 左側按鈕群組 */
        .nav-group {
            display: flex;
            gap: 10px;
        }

        /* 橢圓形按鈕 */
        .custom-nav-btn {
            background-color: #495057; /* 灰色底 */
            color: #fff;
            border: 1px solid #6c757d;
            padding: 6px 18px;
            border-radius: 50px; /* 橢圓形關鍵 */
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .custom-nav-btn:hover {
            background-color: #6c757d;
            color: white;
            transform: translateY(-2px);
        }

        /* 登出按鈕 (紅色) */
        .btn-logout {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-logout:hover {
            background-color: #bb2d3b;
        }
        /* 登入按鈕 (綠色) */
        .btn-login {
            background-color: #198754;
            border-color: #198754;
        }
        .btn-login:hover {
            background-color: #157347;
        }

        /* 標題獨立置中 */
        .page-title {
            text-align: center;
            margin-top: 25px;
            margin-bottom: 10px;
            color: #333;
            font-weight: bold;
            font-size: 28px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

    </style>
</head>
<body>

<!-- 1. 頂部導覽列 (黑底) -->
<div class="header-bar">
    <a href="member.php" class="custom-nav-btn">👥 成員列表</a>
    <a href="contribution_table.php" class="custom-nav-btn">📜 任務表</a>
    <?php if($_SESSION["username"]<>'guest'){
            echo "<a href='logout.php' class='custom-nav-btn btn-logout'>🚪 登出</a>";
        }
        else{
            echo "<a href='go_to_log_in.php' class='custom-nav-btn btn-login'>🚪 登入</a>";
        } ?>

</div>

<!-- 2. 標題區塊 (獨立移到下面置中) -->
<div class="page-title">
    🛡️ 貢獻紀錄 (Circuit)
</div>

<div class="container">
    <!-- 搜尋表單 -->
    <form method="GET" class="row g-3 mb-4 p-3 bg-light rounded border">
        <div class="col-md-4">
            <input type="text" name="search_name" class="form-control" placeholder="搜尋成員姓名..." value="<?php echo htmlspecialchars($s_name); ?>">
        </div>
        <div class="col-md-4">
            <input type="text" name="search_mission" class="form-control" placeholder="搜尋任務類型..." value="<?php echo htmlspecialchars($s_mission); ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary w-100">🔍 搜尋紀錄</button>
        </div>
    </form>

    <div class="d-flex justify-content-between mb-3">
        <h4>紀錄明細</h4>
        <?php if($_SESSION["username"]<>'guest'){ 
            echo "<a href='contribution_circuit_add.php' class='btn btn-success'>+ 新增紀錄</a>";
        } ?>
    </div>

    <table class="table table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>成員姓名</th>
                <th>執行任務</th>
                <th>獲得點數</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["record_id"] . "</td>";
                    echo "<td>" . htmlspecialchars($row["MemberName"]) . "</td>";
                    echo "<td><span class='badge bg-info text-dark'>" . htmlspecialchars($row["Mission_type"]) . "</span></td>";
                    echo "<td class='fw-bold text-success'>+" . $row["point"] . "</td>";
                    
                    // 修改這裡：按鈕改成呼叫 JS 函數
                    if($_SESSION["username"]<>'guest'){
                        echo "<td>
                            <a href='contribution_circuit_edit.php?id=" . $row["record_id"] . "' class='btn btn-sm btn-warning'>編輯</a>
                            <button onclick='confirmDelete(" . $row["record_id"] . ")' class='btn btn-sm btn-danger'>刪除</button>
                          </td>";
                    }
                    else{
                        echo "<td></td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center text-muted'>查無資料</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    // SweetAlert2 刪除確認特效
    function confirmDelete(id) {
        Swal.fire({
            title: '確定要刪除這筆紀錄嗎？',
            text: "刪除後積分也會被扣除喔！",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '是的，刪除！',
            cancelButtonText: '取消'
        }).then((result) => {
            if (result.isConfirmed) {
                // 如果使用者按確定，導向刪除頁面
                window.location.href = 'contribution_circuit_delete.php?id=' + id;
            }
        })
    }
</script>

</body>
</html>
