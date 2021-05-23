<?php
require_once 'config.php';

if(isset($_POST['lietotajvarda_submit'])){

    if(!isset($_SESSION['token1']) || $_SESSION['token1'] != $_POST['username_token'] ) {
        //CSRF ATTACK
        header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
        exit();
    } else{
    $username = htmlspecialchars($_POST['lietotajvards']);
    if(invalidUsername($username)){
        header("location: ../rooms.php?error=bad_username&username=$username&data=show");
        exit();
    }
        $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute(array($username));
        if ($stmt->rowCount() == 0) {
            $stmt = $conn->prepare('UPDATE users SET username = ?  WHERE userID = ?');
            $stmt->execute(array( htmlspecialchars($_POST['lietotajvards']),$_SESSION['userID']));
            $_SESSION['userName'] = htmlspecialchars($_POST['lietotajvards']);
            header("location: ../rooms.php?error=changed&data=show");
        } else{
            header("location: ../rooms.php?error=taken&username=$username&data=show");
            exit();
        }
    }
}else {
    header("location: ../index.php");
}
