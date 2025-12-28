<html>
 <head>
 <title>$result</title>
 </head>
 <script>


 </script>
 <body>
 <?php
 require_once 'db_conn.php';
    date_default_timezone_set('Asia/Taipei');
    function write_log_db($dba, $Acc, $result) {
    $ip   = $_SERVER['REMOTE_ADDR'];
    $time = date("Y-m-d H:i:s");
    $stmt = $dba->prepare("INSERT INTO record (input_acc, ip, timesss, result) VALUES (?, ?, ?, ?)");
    $stmt->execute([$Acc, $ip, $time, $result]);
    }
session_start();


$ipa = $_SERVER['REMOTE_ADDR'];
$lock_interval = 30;
$max_fail = 5;
$sql = "SELECT COUNT(*) FROM record 
        WHERE ip=? AND result=0 AND timesss > DATE_SUB(NOW(), INTERVAL $lock_interval SECOND)";
$stmt = $conn->prepare($sql);
$stmt->execute([$ipa]);
$fail_count = $stmt->get_result()->fetch_row()[0];
if ($fail_count >= $max_fail) {
    echo "<script>alert('你被鎖定了！'); window.location.href='index.php';</script>";
    exit();
}

    // 取得表單資料
        $Acc = htmlspecialchars($_POST['Acc']);
        $Pass = htmlspecialchars($_POST['Pass']);
        $Pass = hash('sha256',$Pass);
        //check.php
        $sql = "SELECT * FROM member WHERE Name=? AND password=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$Acc, $Pass]);
        $result = $stmt->get_result();
        //以上寫法是為了防止「sql injection」
        if($result->num_rows === 1){
            $row = $result->fetch_assoc();
            $_SESSION['username'] = $row['Name'];
            $_SESSION['logged_in'] = true;
            write_log_db($conn, $Acc, TRUE);
            header("Location:member.php");
            exit();
        }

        write_log_db($conn, $Acc, FALSE);
        echo "<script>alert('登入失敗！'); window.location.href='index.php';</script>";
 ?>
 </body>
</html>