<?php
require 'databasecontroll.php';

$stmt = $conn->prepare('SHOW TABLE STATUS FROM chat');
$stmt->execute();

$tables = array();
    while ($table = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tables[] = $table['Name'];
        echo $table['Name'];
    }

    foreach ($tables as $table) {
        if($table == 'password_reset' || $table == 'chatrooms'){
            continue;
        }
        if($table == 'users'){
            $stmt = $conn->prepare('DELETE FROM '.$table.' WHERE userID = :id ');
            $stmt->bindValue(':id', '49643227acaaa');
            
        }else{
            $stmt = $conn->prepare('DELETE FROM '.$table.' WHERE user_id = :id');
            $stmt->bindValue(':id', '49643227acaaa');
        }
        $stmt->execute();  
    }


