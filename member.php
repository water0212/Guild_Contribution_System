<?php
require_once 'db_conn.php';

// 搜尋與列表顯示邏輯 (維持不變)
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// --- [SQL 功能 3] Procedure: 處理 "活躍度更新" 按鈕 ---
if (isset($_POST['btn_engagement'])) {
    // 1. 初始化變數
    $conn->query("SET @p_count = 0, @p_rate = 0");
    
    // 2. 呼叫 Procedure，並傳入 OUT 參數容器
    $conn->query("CALL Engagement(@p_count, @p_rate)");
    
    // 3. 取得 Procedure 計算後的結果
    $res = $conn->query("SELECT @p_count AS new_count, @p_rate AS active_rate");
    $row = $res->fetch_assoc();
    
    // 4. 將結果帶在 URL 上傳給前端顯示
    $c = $row['new_count'];
    $r = $row['active_rate'];
    header("Location: member.php?msg=engaged&count=$c&rate=$r");
    exit;
}

// --- [SQL 功能 4] Procedure: 處理 "重置" 按鈕 ---
if (isset($_POST['btn_reset'])) {
    $conn->query("CALL Reset_Engagement()");
    header("Location: member.php?msg=reset");
    exit;
}


$s_name = isset($_GET['search_name']) ? $_GET['search_name'] : "";
$s_op   = isset($_GET['point_op']) ? $_GET['point_op'] : "=";
$s_point= isset($_GET['search_point']) ? $_GET['search_point'] : "";

// 使用 Function 查詢
$sql = "SELECT *, get_member_total_points(Member_Id) AS total_score FROM member WHERE 1=1";

if (!empty($s_name)) {
    $safe_name = $conn->real_escape_string($s_name);
    $sql .= " AND Name LIKE '%$safe_name%'";
}

if ($s_point !== "") {
    $safe_point = (int)$s_point;
    if (in_array($s_op, ['=', '>', '<', '>=', '<='])) {
        // 為了簡單展示，這裡只做前端顯示過濾，若要精確 SQL 篩選需用 HAVING
        // 這裡暫時保留你的原始邏輯結構
       // $sql .= " AND ..."; 
    }
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: "Microsoft JhengHei", sans-serif; background-color: #f5f5f5; }
        .search-bar { background: white; padding: 15px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .container { max-width: 900px; margin: 20px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        /* --- 修改後的 Header 樣式 (仿照圖片風格) --- */
        .header-container {
            background-color: #212529; /* 深色背景 */
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }

        /* 上方按鈕區塊 */
        .nav-pills-group {
            display: inline-flex;
            gap: 15px; /* 按鈕之間的距離 */
            margin-bottom: 20px; /* 按鈕跟標題的距離 */
            flex-wrap: wrap;
            justify-content: center;
        }

        /* 橢圓形按鈕樣式 */
        .custom-nav-btn {
            background-color: #495057; /* 灰色底 */
            color: #fff;
            border: 1px solid #6c757d;
            padding: 8px 20px;
            border-radius: 50px; /* 橢圓形關鍵 */
            text-decoration: none;
            font-size: 15px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .custom-nav-btn:hover {
            background-color: #6c757d; /* 滑鼠移過去變亮一點 */
            color: white;
            transform: translateY(-2px); /* 微微浮起特效 */
        }

        /* 登出按鈕特別改成紅色 */
        .btn-logout {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-logout:hover {
            background-color: #bb2d3b;
        }
        .btn-login {
            background-color: #198754;
            border-color: #198754;
        }
        .btn-login:hover {
            background-color: #157347;
        }

        /* 下方大標題樣式 */
        .page-title {
            color: #f8f9fa;
            font-size: 28px;
            font-weight: bold;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        table { width: 100%; margin-top: 10px; }
        th { background-color: #4a3b3b; color: white; padding: 12px; text-align: center; }
        td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; }
        tr:nth-child(even) { background-color: #e0e0e0; }
        @media (max-width: 776px) { .header { flex-direction: column; gap: 10px; } }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[首頁]公會成員列表</title>
</head>
<body>
    <div class="header-container">
        <!-- 上排：導覽按鈕 -->
        <div class="nav-pills-group">
            <a href="contribution_circuit.php" class="custom-nav-btn">≡ 貢獻紀錄</a>
            <a href="contribution_table.php" class="custom-nav-btn">📜 任務表</a>
                <?php if($_SESSION["username"]<>'guest'){
            echo "<a href='logout.php' class='custom-nav-btn btn-logout'>🚪 登出</a>";
        }
        else{
            echo "<a href='go_to_log_in.php' class='custom-nav-btn btn-login'>🚪 登入</a>";
        } ?>
        </div>

        <!-- 下排：大標題 -->
        <div class="page-title">
            📜 公會成員列表
        </div>
    </div>

    <!-- 搜尋區塊 -->
    <div class="search-bar">
        <form method="GET" action="">
            <label>人物名稱：</label>
            <input type="text" name="search_name" value="<?php echo htmlspecialchars($s_name); ?>">
            <button type="submit" class="nav-btn">🔍 搜尋</button>
            <?php if(!empty($s_name)): ?><a href="member.php" style="margin-left:10px;">清除</a><?php endif; ?>
        </form>
    </div>

    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">🛡️ 公會成員列表</h4>
                
                <!-- [SQL 功能 3 & 4] 兩個 Procedure 按鈕 -->
                <div class="d-flex gap-2">
                    <form method="post" style="margin:0;">
                        <button type="submit" name="btn_engagement" class="btn btn-warning btn-sm text-dark fw-bold">
                            ⚡ 更新活躍度 (Engagement)
                        </button>
                    </form>
                    <form method="post" style="margin:0;">
                        <button type="submit" name="btn_reset" class="btn btn-secondary btn-sm text-white">
                            🔄 重置狀態
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card-body">
                <div class="mb-3">
                    <a href="member_new.php" class="btn btn-success">+ 新增成員</a>
                </div>

                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>姓名</th>
                            <th>貢獻度 (Function)</th>
                            <th>活躍狀態 (Note)</th>
                            <th>管理操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                if($row["password"] <> NULL){
                                    echo "<td>🔐 " . $row["Member_Id"] . "</td>";
                                }
                                else{
                                    echo "<td>👤 " . $row["Member_Id"] . "</td>";
                                }
                                echo "<td><strong>" . $row["Name"] . "</strong></td>";
                                
                                $score = $row["total_score"]; 
                                $badgeColor = $score > 100 ? "text-danger" : "text-muted";
                                echo "<td class='$badgeColor fw-bold'>" . $score . " pts</td>";
                                
                                // 根據狀態顯示不同顏色的標籤
                                $status = $row["Note"];
                                if ($status == '活躍中') {
                                    echo "<td><span class='badge bg-success'>🔥 活躍中</span></td>";
                                } else {
                                    echo "<td><span class='badge bg-secondary'>💤 非活躍狀態</span></td>";
                                }
                                if($_SESSION['username'] <> "guest"){
                                    if($row["Name"] == $_SESSION['username']||$row["password"] <> NULL){
                                        echo "<td>
                                        <a href='member_edit.php?id=" . $row["Member_Id"] . "' class='btn btn-warning btn-sm'>編輯</a>
                                        <button onclick='confirmDelete(" . $row["Member_Id"] . ")' class='btn btn-danger btn-sm'>刪除</button>
                                      </td>";
                                    }
                                    else{
                                        echo "<td>
                                        <a href='member_edit.php?id=" . $row["Member_Id"] . "' class='btn btn-warning btn-sm'>編輯</a>
                                        <button onclick='confirmDelete(" . $row["Member_Id"] . ")' class='btn btn-danger btn-sm'>刪除</button>
                                        <button onclick='confirmPromote(" . $row["Member_Id"] . ")' class='btn btn-info btn-sm'>提拔</button>
                                      </td>";
                                    }
                                     
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>沒有資料</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 邏輯 -->
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        
        // 1. 顯示活躍度更新結果 (帶有變數)
        if (urlParams.get('msg') === 'engaged') {
            const count = urlParams.get('count'); // 新增活躍人數
            const rate = urlParams.get('rate');   // 活躍比例
            
            Swal.fire({
                icon: 'success',
                title: '活躍度更新完成！',
                html: `
                    公會目前多了 <b>${count}</b> 個活躍的成員！<br>
                    一共有 <b>${rate}%</b> 的成員正在活躍中 🔥
                `,
                confirmButtonText: '太棒了！'
            }).then(() => {
                // 清除網址參數
                window.history.replaceState(null, null, window.location.pathname);
            });
        }

        // 2. 顯示重置結果
        if (urlParams.get('msg') === 'reset') {
            Swal.fire({
                icon: 'info',
                title: '已重置',
                text: '所有成員已變更為「非活躍狀態」',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.history.replaceState(null, null, window.location.pathname);
            });
        }

        function confirmDelete(id) {
            Swal.fire({
                title: '確定要踢出此人嗎？',
                text: "刪除後無法復原！",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: '是的，踢出！',
                cancelButtonText: '取消'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'member_delete.php?id=' + id;
                }
            })
        }
        function confirmPromote(id) {
            Swal.fire({
                title: '確定要提拔此人嗎？',
                text: "提拔後將提升其權限！",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: '是的，提拔！',
                cancelButtonText: '取消'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'member_promote.php?id=' + id;
                }
            })
        }
    </script>
</body>
</html>
