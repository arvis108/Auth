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
            $message = htmlspecialchars($_REQUEST['message']);
            if(strlen($message) > 510) {
                echo 'Jūsu sūtītā ziņa ir pārāk gara!';
                break;
            }

            if($message == '') {
                break;
            }

            $query = $conn->prepare("INSERT INTO messages SET text = ?,fk_userID_messages = ?,fk_roomID_messages = ?");
            $query->execute([$message,$_SESSION['userID'],$_GET['room']]);
            echo 1;
            break;
        case 'getMessages':
            $query = $conn->prepare("SELECT a.text,a.time,b.username FROM messages a,users b WHERE a.fk_roomID_messages = ? and a.fk_userID_messages = b.userID;");
            $query->execute([$_GET['room']]);
            $msgs = $query->fetchAll(PDO::FETCH_OBJ);
            $chat ='';
            foreach($msgs as $msg){
                if($msg->username == $_SESSION['userName']){ 
                    $username = 'Es';
                } else {
                    $username = $msg->username;
                }
                    $chat .= '<div class ="mField"> <div class="message_data">'
                    .$msg->time.'  '.$username.'</div><div class = "chat_msg">'.$msg->text.'</div></div>';
                    
                }
                echo $chat;
                break;
        
        default:
            break;
    }
}else{
    header("location: ../index.php");
}
?>