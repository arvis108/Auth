<?php
session_start();
require_once './vendor/autoload.php';
require 'includes/functions.ini.php';
require 'includes/databasecontroll.php';
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    include './includes/logout.inc.php';
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
//ja lietotājs nav ielogojies
//vai arī norādītai lietotāja identifikators neeksisē
//tiek aizsūtīts uz sākuma lapu
if(!isset($_SESSION['userName']) || !checkLoginState($conn,$_SESSION['userName'],$_SESSION['pwd']) ){
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
$pic = htmlspecialchars($_GET['room']);
$profpic = $pic.'.jpg';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/chat_styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Document</title>
    <style type="text/css">

    .wrapper {
        background-image: url('<?php echo "img/$profpic" ;?>');
        background-position: center; 
        background-repeat: no-repeat; 
        background-size: cover; 
    }
</style>
</head>

<body>
    <div class="wrapper">
    <div class="flex">
        <h1>Welcome to <?php echo $_GET['room'];?> Room!</h1>
        <form action="./includes/logout.inc.php" method="POST" class="logout_form">
        <input type="submit" name="logout" value="Log-Out">
        
    </form></div>

        <div class="chat_wrapper">
            <div class="chat" id="chat">
            </div>
            <form method="POST" class="send_form flex">
                <div class="button">
                    <a href="rooms.php">Atpakaļ</a>
                </div>
                <textarea name="message" id="message" class="textarea"></textarea>
                <input type="submit" value="Sūtīt" class="send_btn">
            </form>
            <p id="message_error"></p>

        </div>
        
    </div>
</body>
<script>

var $chat = $(".chat");
var chatHeight = $chat.innerHeight();
var room = "<?php echo $_GET['room']?>";

$(document).ready(function(){
    loadChat();
});

$('#message').keyup(function(e) { 
    var message = $(this).val();
    if(e.which == 13){
        $.post('includes/chat.ini.php?action=SendMessage&message='+message+'&room='+room,function(response){
            if(response == 1)
            {
            loadChat();
            $('#message').val('');
        } else{
            $('#message').val('Sūtītais teksts saturēja pārāk daudzas rakstzīmes vai arī radās cita kļūme');
        }
            
        });
    }
});
$(".send_form").submit(function( event ) {
    event.preventDefault();
    var message = $('#message').val();
    $.ajax({
                    type: 'post',
                    url: 'includes/chat.ini.php?action=SendMessage&message='+message+'&room='+room+'',
                    data: $('.send_form').serialize(),
                    success: function(response) {
                        if(response == 1)
                        {
                        loadChat();
                        $('#message').val('');
                        } else{
                        $('#message').val('Sūtītais teksts saturēja pārāk daudzas rakstzīmes vai arī radās cita kļūme');
                        }
                    }
                });
// your code here
});


function loadChat(){
    var room = "<?php echo $_GET['room']?>";
    $.post('includes/chat.ini.php?action=getMessages&room='+room,function(response){
            $('.chat').html(response);
            var d = $('#chat');
            d.scrollTop(d.prop("scrollHeight"));
        });
}


setInterval(() => {
    loadChat();
}, 4000);
</script>
</html>