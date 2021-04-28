<?php
date_default_timezone_set('Europe/Riga');
require 'databasecontroll.php';
session_start();
$datetime = date('Y-m-d H:i:s');
$Logstmt = $conn->prepare('UPDATE logs set loginEndTime = ? WHERE user_id=?');
$Logstmt->execute([$datetime,$_SESSION['userID']]);
$Statusstmt = $conn->prepare('UPDATE users set status = "0" where userID=?');
$Statusstmt->execute([$_SESSION['userID']]);
$providerstmt = $conn->prepare('UPDATE users set provider = " " where userID=?');
$providerstmt->execute([$_SESSION['userID']]);
session_unset();
session_destroy();
header("Location:../index.php");
exit();


//facebook log out nestrādā

