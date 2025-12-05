<?php
// 設定資料庫連線變數
$servername = "localhost";
$username = "root";
$password = "water0212"; // XAMPP 預設為空，若有設定請自行修改
$dbname = "Guild_Contribution_System";

// 建立連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線是否成功
if ($conn->connect_error) {
    die("連線失敗 (Connection failed): " . $conn->connect_error);
}

// 設定編碼為 utf8mb4 (支援中文與 Emoji)
$conn->set_charset("utf8mb4");
?>
