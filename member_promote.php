<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
require_once "db_conn.php";

if (!isset($_GET['id'])) {
    die("缺少會員 ID");
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM member WHERE Member_Id = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("找不到指定的會員");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>編輯成員</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4 shadow">
        <h3 class="mb-4">成為公會長(們)</h3>

        <form action="member_promote_action.php" method="post">
            <input type="hidden" name="Member_Id" value="<?= $row['Member_Id'] ?>">

            <div class="mb-3">
                <label class="form-label">姓名</label>
                <input type="text"  name="Name" class="form-control" value="<?= htmlspecialchars($row['Name']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">新增密碼</label>
                <input type="text" name="Pass" class="form-control" value="" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,20}" required>
            </div>

            <button type="submit" class="btn btn-primary">儲存變更</button>
            <a href="member.php" class="btn btn-secondary">返回</a>
        </form>
    </div>
</div>

</body>
</html>
