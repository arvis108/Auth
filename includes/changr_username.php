<?php
session_start();
require 'databasecontroll.php';
require 'functions.ini.php';

if(isset($_POST['lietotajvarda_submit'])){
    $username = htmlspecialchars($_POST['lietotajvards']);
    if(invalidUsername($username)){
        header("location: ../rooms.php?error=bad_username&username=$username");
        exit();
    }
        $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute(array($username));
        if ($stmt->rowCount() == 0) {
            $stmt = $conn->prepare('UPDATE users SET username = ?  WHERE userID = ?');
            $stmt->execute(array( htmlspecialchars($_POST['lietotajvards']),$_SESSION['userID']));
            $_SESSION['userName'] = htmlspecialchars($_POST['lietotajvards']);
            header("location: ../rooms.php?error=changed");
        } else{
            header("location: ../rooms.php?error=taken&username=$username");
            exit();
        }
}else {
    header("location: ../index.php");
}
