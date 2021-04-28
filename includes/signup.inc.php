<?php
require 'databasecontroll.php';
require 'userClass.php';
session_start();
if (isset($_POST['rsubmit'])) {
    $errors = array();
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $pwd = $_POST['pwd'];
    $pwdRepeat = $_POST['pwdRepeat'];

    if(emptyInput($username, $email, $pwd,$pwdRepeat)){
        array_push($errors,'Aizpildiet visus laukus');
    }
    if(invalidUsername($username)){
        array_push($errors,'Lietotājvārds var saturēt ciparus,burtus un .-_ simbolus, minimālais garums 8 simboli');
    }
    if(invalidEmail($email)){
        array_push($errors,'Nepareiza E-pasta adrese');
    }  
    if(empty($_POST['term_cb'])){
        array_push($errors,'Apstipriniet lietošanas noteikumus un privātuma politiku');
    }
    if(pwdTest($pwd)){
        array_push($errors,'Paroles minimālais garums ir 8 simboli');
    }
    if(pwdCharTest($pwd)){
        array_push($errors,'Parole satur pārāk daudz atkārtojošos simbolus');
    }
    if(pwdUsernameTest($username,$email,$pwd)){
        array_push($errors,'Parole ir pārāk līdzīga lietotājvārdam un/vai e-pastam');
    }
    if(badPwd($pwd)){
        array_push($errors,'Izvēlētā parole ir kompromitēta! Lūdzu izvēlieties drošāku paroli');
    }
    if(pwdMatch($pwd,$pwdRepeat)){
        array_push($errors,'Paroles nesakrīt');
    }

    if (count($errors) == 0) {
    $user = new Users($conn);
    $user->register($username,$email,$pwd);
    }else{
        $_SESSION['errors']= $errors;
        header("location: ../signup.php?username=$username&email=$email");
    }

} else {
    header("location: ../index.php");
}
