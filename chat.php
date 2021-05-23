<?php
require_once 'includes/config.php';

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
$stmt = $conn->prepare('SELECT ID FROM chatRooms WHERE name = ?');
$stmt->execute([$_GET['room']]);
$roomID = $stmt->fetchColumn();
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
    <title>Čats</title>
    <style type="text/css">
    .wrapper {
        background-image: url('<?php echo "img/$profpic" ;?>');
    }
</style>
</head>

<body>
    <div class="wrapper">
    <div class="flex">
        <div class="text-bg">
            <h1>Laipni aicināti <?php echo $_GET['room'];?> čata istabā!</h1>
        </div>
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
                <textarea name="message" id="message" class="textarea" placeholder="Raksti šeit:"></textarea>
                <input type="submit" value="Sūtīt" class="send_btn">
            </form>
            <p id="message_error"></p>

        </div>

    </div>
</body>
<script>

var $chat = $(".chat");
var chatHeight = $chat.innerHeight();
var room = "<?php echo $roomID?>";

$(document).ready(function(){
    loadChat();
    <?php
if(isset($_SESSION["userID"]))
{
?>
    function update_user_activity(){
    var action = 'update_time';
        $.ajax({
            url:"includes/user_statuss.php",
            method:"POST",
            data:{action:action},
            success:function(data){
                }
        });
    }
    update_user_activity();
    setInterval(function(){ 
        update_user_activity();
    }, 3000);

<?php
}
else
{
?>
fetch_user_login_data();

setInterval(function(){
        fetch_user_login_data();
    }, 3000);

    function fetch_user_login_data(){
        var action = "fetch_data";
        $.ajax({
        url:"includes/user_statuss.php",
        method:"POST",
        data:{action:action},
        success:function(data)
        {
        }
        });
    }
    <?php
}
?>

});

$('#message').keyup(function(e) {
    if(e.which == 13){
        var message = $(this).val();
        //var message = sanitizeHTML(unsmessage);
        $.post('includes/chat.ini.php?action=SendMessage&message='+sanitize(message)+'&room='+room,function(response){
            if(response == 1){
                loadChat();
                $('#message').val('');
                $('#message_error').text('');
            } else{
                $('#message').val('');
                $('#message_error').text(response);
            }

        });
    }
});

$(".send_form").submit(function( event ) {
    event.preventDefault();
    var message = $('#message').text();
    $.ajax({
                    type: 'post',
                    url: 'includes/chat.ini.php?action=SendMessage&message='+message+'&room='+room+'',
                    data: $('.send_form').serialize(),
                    success: function(response) {
                        if(response == 1){
                            loadChat();
                            $('#message').val('');
                            $('#message_error').val('');
                        } else{
                            $('#message').val('');
                            $('#message_error').val(response);
                        }
                    }
                });
});

function loadChat(){
    $.post('includes/chat.ini.php?action=getMessages&room='+room,function(response){
            $('.chat').html(response);
            var d = $('#chat');
            d.scrollTop(d.prop("scrollHeight"));
        });
}
function sanitize(string) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#x27;',
        "/": '&#x2F;',
    };
    const reg = /[&<>"'/]/ig;
    return string.replace(reg, (match)=>(map[match]));
}


 setInterval(() => {
     loadChat();
 }, 4000);
</script>
</html>