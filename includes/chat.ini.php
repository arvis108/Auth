<?php 
if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
    include '../index.php';
}
require 'databasecontroll.php';
session_start();
if(isset($_REQUEST['action'])){
    switch ($_REQUEST['action']) {
        case 'SendMessage':
            $message = htmlentities(strip_tags($_REQUEST['message']));
            if(strlen($message) > 255){
                echo 0;
                break;
            }
            $query = $conn->prepare("INSERT INTO messages SET text = ?,fk_userID_messages = ?,chatRoom = ?");
            $query->execute([$message,$_SESSION['userID'],$_GET['room']]);
            echo 1;
            break;
        case 'getMessages':
            $query = $conn->prepare("SELECT a.text,a.time,b.username FROM messages a,users b WHERE chatRoom = ? and a.fk_userID_messages = b.userID;");
            $query->execute([$_GET['room']]);
            $msgs = $query->fetchAll(PDO::FETCH_OBJ);
            $chat ='';
          foreach($msgs as $msg){
                $chat .= '<div class ="mField">'.$msg->time.'  '.$msg->username.'   '.$msg->text.'</div>';
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