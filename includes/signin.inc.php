<?php
require_once 'config.php';

if (isset($_POST['psubmit'])) {
    $user = new Users($conn);
    if ($user->login(htmlspecialchars($_POST['name_email']),htmlspecialchars($_POST['password']))) {
        header("location: ../rooms.php?error=none");
    } else {
        header("location: ../index.php?error=unknown");
    }
} else {
    header("location: ../index.php");
}
