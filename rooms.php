<?php 
session_start();
require 'includes/functions.ini.php';
require 'includes/databasecontroll.php';
//ja lietotājs nav ielogojies
//vai arī norādītai lietotāja identifikators neeksisē
//tiek aizsūtīts uz sākuma lapu
if(!isset($_SESSION['userID']) || !checkLogin($conn,$_SESSION['userID']) ){
    header("location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/room_styles.css">
    <title>Document</title>
</head>
<body>
<div class="wrapper">
    <nav>
        <h1>Welcome <?php echo $_SESSION['userID'];?>!</h1>
        <form action="./includes/logout.inc.php" method="POST" class="logout">
            <input type="submit" name="logout" value="Log-Out">
        </form>
    </nav>
    <div class="room">
    <?php 
    $stmt = $conn->prepare('SELECT name FROM chatRooms');
    $stmt->execute();
    while ($rooms = $stmt->fetch(PDO::FETCH_ASSOC)) {
        foreach ($rooms as $value) {
            echo '<form class="rooms_form" action="chat.php?room='.$value.'" method="POST">';
            echo '<p>'.htmlspecialchars($value).'</p>';
            echo '<input type="submit" class="btn effect01" name="roomSubmit" value="Pievienoties">';
            echo '</form>';
            echo '<img src="img/'.htmlspecialchars($value).'.jpg">';
        }
    } ?>
    </div>

    <div class="userList">


    </div>
    </div>
</body>

</html>