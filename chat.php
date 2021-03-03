<?php
session_start();
require_once './vendor/autoload.php';
require 'includes/functions.ini.php';
require 'includes/databasecontroll.php';
//ja lietotājs nav ielogojies
//vai arī norādītai lietotāja identifikators neeksisē
//tiek aizsūtīts uz sākuma lapu
if(!isset($_SESSION['userID']) || !checkLogin($conn,$_SESSION['userID']) ){
    header("location: index.php");
}

//lapai var piekļūt tikai no rooms.php lapas
if(!isset($_GET['room'])){
    header("location: rooms.php");
} else{
    //veikta pārbaude, vai get metodē sūtītie dati ir atbilstoši(white listoti)
require 'includes/databasecontroll.php';
$stmt = $conn->prepare('SELECT name FROM chatRooms');
$stmt->execute();
$white_list = array(); 
while ($rooms = $stmt->fetch(PDO::FETCH_ASSOC)) {
    foreach ($rooms as $value) {
         $white_list[] = $value;     
    }
}
if (!in_array($_GET['room'], $white_list)) {
    header("location: rooms.php");
}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/chat_styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Document</title>
</head>

<body>
    <div class="wrapper">
    <div class="flex">
        <h1>Welcome to <?php echo $_GET['room'];?> Room!</h1>
        <form action="./includes/logout.inc.php" method="POST">
        <input type="submit" name="logout" value="Log-Out">
    </form></div>

        <div class="chat_wrapper">
            <div class="chat" id="chat">
            </div>
            <form method="POST">
                <textarea name="message" id="message" cols="30" rows="10" class="textarea"></textarea>
            </form>

        </div>
    </div>
</body>
<script>

var $chat = $(".chat");
var chatHeight = $chat.innerHeight();
var chatIsAtBottom = true;


$(document).ready(function(){
    loadChat();
});
$('#message').keyup(function(e) {
    var message = $(this).val();
    var room = "<?php echo $_GET['room']?>";
    if(e.which == 13){
        $.post('includes/chat.ini.php?action=SendMessage&message='+message+'&room='+room,function(response){
            if(response == 1)
            {
            loadChat();
            $('#message').val('');
        } else{
            $('#message').val('There was an error');
        }
            
        });
    }
});
function loadChat(){
    var room = "<?php echo $_GET['room']?>";
    $.post('includes/chat.ini.php?action=getMessages&room='+room,function(response){
            $('.chat').html(response);
            if(chatIsAtBottom){
    $chat.stop().animate({
      scrollTop: $chat[0].scrollHeight - chatHeight
    },600);
  }
            // var d = $('#chat');
            // d.scrollTop(d.prop("scrollHeight"));
        });
}

function checkBottom(){
  chatIsAtBottom = $chat[0].scrollTop + chatHeight >= $chat[0].scrollHeight;
}

$chat.scrollTop( $chat[0].scrollHeight ).on("scroll", checkBottom);


setInterval(() => {
    loadChat();
}, 4000);
</script>
</html>