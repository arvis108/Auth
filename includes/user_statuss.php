<?php
require_once 'config.php';

if(isset($_POST["action"]))
{
    //saglabā datu bāzē lietotājā pēdējās aktivitātes laiku
if($_POST["action"] == "update_time")
{
$time = date("Y-m-d H:i:s");
$stmt = $conn->prepare('UPDATE users SET last_activity = ? WHERE userID = ?');
$stmt->execute([$time,$_SESSION['userID']]);
}

//nepieciešams, lai gadījumā, ja lietotājs nav nospiedis izlogošanās pogu, datu bāzes lauka statuss vērtību izmainītu uz 0 
//ja lietotājs 10 minūšu laikā nav veicis nekādu aktivitāti
if($_POST["action"] == "fetch_data"){

    $stmt1 = $conn->prepare('SELECT * FROM users WHERE last_activity < DATE_ADD(NOW(), INTERVAL -10 MINUTE)');
    $stmt1->execute();

    while($ids = $stmt1->fetch(PDO::FETCH_ASSOC)){
                        $stmt2 = $conn->prepare('UPDATE users set status = "0" where userID=?');
                        $stmt2->execute([$ids['userID']]);
                    }
}
}
