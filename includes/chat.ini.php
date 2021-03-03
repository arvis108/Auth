<?php 
require 'databasecontroll.php';
session_start();
if(isset($_REQUEST['action'])){
    switch ($_REQUEST['action']) {
        case 'SendMessage':
            $query = $conn->prepare("INSERT INTO messages SET text = ?,user_id = ?,chatRoom = ?");
            $query->execute([$_REQUEST['message'],$_SESSION['userID'],$_GET['room']]);
            echo 1;
            break;
        case 'getMessages':
            $query = $conn->prepare("SELECT a.text,a.time,b.username FROM messages a, users b WHERE a.user_id = b.userID and chatRoom = ?;");
            $query->execute([$_GET['room']]);
            $msgs = $query->fetchAll(PDO::FETCH_OBJ);
            $chat ='';
          foreach($msgs as $msg){
                $chat .= '<div class ="mField">'.$msg->time.'  '.$msg->username.'   '.$msg->text .'</div>';
            }
            echo $chat;
            break;
        
        default:
            # code...
            break;
    }
}else{
    header("location: ../index.php");
}
?>