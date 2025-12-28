<?php
// 這裡要填 Alwaysdata 給你的資訊
$servername = "mysql-water.alwaysdata.net"; 
$username = "water_guild";     // 例如 guildteam_admin
$password = "water0212";           // 例如 team1234
$dbname = "water_guild_db";       // 例如 guildteam_guild_db

// 建立連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>