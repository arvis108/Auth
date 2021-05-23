<?php
require_once 'config.php';

//Reset OAuth access token
$google_client->revokeToken();

$datetime = date('Y-m-d H:i:s');
$Logstmt = $conn->prepare('UPDATE logs set loginEndTime = ? WHERE fk_userID_logs=?');
$Logstmt->execute([$datetime,$_SESSION['userID']]);

$Statusstmt = $conn->prepare('UPDATE users set status = "0" where userID=?');
$Statusstmt->execute([$_SESSION['userID']]);



session_unset();
session_destroy();
header("Location:../index.php");
exit();


