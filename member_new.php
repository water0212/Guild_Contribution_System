<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
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
        <h3 class="mb-4">新增會員資料</h3>
        <form action="member_new_action.php" method="post">

            <div class="mb-3">
                <label class="form-label">姓名</label>
                <input type="text"  name="Name" class="form-control" value="" required>
            </div>

            <button type="submit" class="btn btn-primary">新增完成</button>
            <a href="member.php" class="btn btn-secondary">返回</a>
        </form>
    </div>
</div>

</body>
</html>
