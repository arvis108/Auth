<?php
require_once 'config.php';

if(isset($_POST['parole_submit'])){
    if($_SESSION['token1'] != $_POST['pwd_token'] ) {
        //CSRF ATTACK
        header("location: ../index.php");
    }
    $errors = array();
    $pwd = $_POST['oldpwd'];
    $pwdNew = $_POST['newpwd'];
    $pwdNewRepeat = $_POST['newpwdr'];

    $stmt = $conn->prepare('SELECT * FROM users WHERE userID = ?');
    $stmt->execute(array($_SESSION['userID']));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $pwd_peppered = hash_hmac("sha256", $pwd, PEPPER);
        if (password_verify($pwd_peppered, $user['password'])) {
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
                $pwd_peppered = hash_hmac("sha256", $pwdNew, PEPPER);
                $hash = password_hash($pwd_peppered, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare('UPDATE users set password = ? where userID=?');
                $stmt->execute([$hash,$_SESSION['userID']]);

                $datetime = date('Y-m-d H:i:s');
                $Logstmt = $conn->prepare('UPDATE logs set loginEndTime = ? WHERE fk_userID_logs=?');
                $Logstmt->execute([$datetime,$_SESSION['userID']]);

                $Statusstmt = $conn->prepare('UPDATE users set status = "0" where userID=?');
                $Statusstmt->execute([$_SESSION['userID']]);

                session_unset();
                session_destroy();
                header("location: ../index.php?password_changed=true");
                }else{
                    $_SESSION['errors']= $errors;
                    header("location: ../rooms.php?error=pwd&data=show");
                }
        } else {
            header("location: ../rooms.php?error=wrong_pwd&data=show");
        }
    
}else {
    header("location: ../index.php");
}