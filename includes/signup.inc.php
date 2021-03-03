<?php
require 'databasecontroll.php';
require 'userClass.php';
if (isset($_POST['rsubmit'])) {
    $user = new Users($conn);
    if ($user->register(htmlspecialchars($_POST['username']), htmlspecialchars($_POST['email']), htmlspecialchars($_POST['pwd']), htmlspecialchars($_POST['pwdRepeat']))) {
        header("location: ../index.php?error=none");
    } else {
        header("location: ../signup.php?error=unknown");
    }
} else {
    header("location: index.php");
}
