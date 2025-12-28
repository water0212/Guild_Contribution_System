<?php
session_start();
    $_SESSION['username'] = "guest";
    $_SESSION['logged_in'] = true;
    header("Location: member.php");
 ?>