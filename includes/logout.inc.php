<?php
require 'databasecontroll.php';
session_start();
$stmt = $conn->prepare('UPDATE users set status = "0" where userID=?');
$stmt->execute([$_SESSION['userID']]);
session_unset();
session_destroy();
header("Location:../index.php");
exit();


//facebook log out nestrādā

