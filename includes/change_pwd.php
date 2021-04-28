<?php
session_start();
require 'databasecontroll.php';
require 'functions.ini.php';

if(isset($_POST['lietotajvarda_submit'])){
    $errors = array();
    $pwd = $_POST['oldpwd'];
    $pwdNew = $_POST['newpwd'];
    $pwdNewRepeat = $_POST['newpwdrpt'];

    $stmt = $conn->prepare('SELECT * FROM users WHERE userID = ?');
    $stmt->execute(array($_SESSION['userID']));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($pwd, $user['password'])) {
            if(empty($pwdNew) || empty($pwdNewRepeat)){
                array_push($errors,'Aizpildiet visus laukus');
            }
            if(pwdTest($pwdNew)){
                array_push($errors,'Paroles minimālais garums ir 8 simboli');
            }
            if(pwdCharTest($pwdNew)){
                array_push($errors,'Parole satur pārāk daudz atkārtojošos simbolus');
            }
            if(pwdUsernameTest($_SESSION['userName'],$_SESSION['email'],$pwdNew)){
                array_push($errors,'Parole ir pārāk līdzīga lietotājvārdam un/vai e-pastam');
            }
            if(badPwd($pwdNew)){
                array_push($errors,'Izvēlētā parole ir kompromitēta! Lūdzu izvēlieties drošāku paroli');
            }
            if(pwdMatch($pwdNew,$pwdNewRepeat)){
                array_push($errors,'Paroles nesakrīt');
            }
        
            if (count($errors) == 0) {
                $hash = password_hash($pwdNew, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('UPDATE users set password = ? where userID=?');
                $stmt->execute([$hash,$_SESSION['userID']]);
                header("location: ../rooms.php?success");
                }else{
                    $_SESSION['errors']= $errors;
                    header("location: ../rooms.php?error=pwd");
                }
        } else {
            header("location: ../rooms.php?error=wrong_pwd");
        }
    
}else {
    header("location: ../index.php");
}