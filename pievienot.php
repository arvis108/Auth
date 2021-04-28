<?php 
include 'includes/databasecontroll.php';
// if(!isset($_POST['pievienotSubmit'])){
//     header("location: rooms.php");
// }
$stmt = $conn->prepare("SELECT * FROM chatrooms");
$stmt->execute();
while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $rooms_array[] = $res;
}

if(isset($_POST['pievienot'])){
    $nosaukums = htmlspecialchars($_POST['room_name']);
    $image  = htmlspecialchars($_FILES['foto']['name']);

    $target_dir = "img/";
    $target_file = $target_dir.basename($_FILES["foto"]["name"]);
    // Select file type
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Valid file extensions
    $extensions_arr = array("jpg", "jpeg", "png", "gif");

    $stmt = $conn->prepare("INSERT INTO chatrooms (name,foto) VALUES (?,?)");
    $stmt->execute([$nosaukums,$image]);
    if (in_array($imageFileType, $extensions_arr)) {
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_dir . $image);
        header("location: ./pievienot.php?info=Pievienots veiksmigi");
    }
}

if (isset($_GET["action"]) && $_GET["action"] == "del") {

    $stmt = $conn->prepare("DELETE FROM chatrooms WHERE ID = ?");
    if($stmt->execute([$_GET["room_id"]])){
        header("location: ./pievienot.php?info=Istaba izdzesta");
    } else {
        header("location: ./pievienot.php?info=Error");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <title>Document</title>
</head>
<body>
<div class="container bigger" id="container">
    <div class="form-container smaller"> 
        <form action="" method="POST" class="pievienot_form" enctype="multipart/form-data">
            <label for="room_name">Istabas nosaukums:</label>
            <input type="text" name="room_name" id="room">
            <label for="foto">Foto:</label>
            <input type="file" name="foto" id="foto">
            <input type="submit" name="pievienot" value="Pievienot">
        </form>
    </div>
    <div class="container_scroll">
                <h1>Istabu saraksts</h1>
                <table>
                    <tbody>
                <tr>
                    <th style="text-align:left;">Nosaukums</th>
                    <th style="text-align:left;">Foto:</th>
                    <th style="text-align:center;">Dzēst</th>
                    <th style="text-align:center;">Rediģēt</th>
                </tr>
                <?php
                foreach ($rooms_array as $key => $value) {
                   
                ?>
                    <tr>
                        <td style="text-align:left;"><?php echo $rooms_array[$key]["name"]; ?></td>
                        <td style=" text-align:left;"><?php echo $rooms_array[$key]["foto"]; ?></td>
                        <td style="text-align:center;"><a href="pievienot.php?action=del&room_id=<?php echo $rooms_array[$key]["ID"];?>" onclick="return confirm('Vai jūs esat drošs?');">
                                <img src="img/icon-delete.png" alt="Remove Item" />
                            </a>
                        </td>
                        <td style="text-align:center;"><a href="pievienot.php?action=edit&room_id=<?php echo $rooms_array[$key]["ID"];?>" >
                                <img src="img/edit.png" alt="Edit Item" style="width: 20px;
                                height: 20px;" />
                            </a>
                        </td>
                        <?php 
                     if(isset($_GET['action']) && $_GET['action'] == 'edit'){
                        if($rooms_array[$key]["ID"]== $_GET['room_id']){
                           echo '<form action="" method="post">
                           <input type="text" name= "nosaukums" placeholder= "'.$rooms_array[$key]["name"].'">
                           <input type="text" name= "foto" placeholder= "'.$rooms_array[$key]["foto"].'">
                           <input type="submit" name= "rediget" value="Rediģēt" class = "rediget_submit">
                           </form>';  
                        }
                   }
                    ?>
                    </tr>
                    
                <?php
                }
                ?>
            </tbody>
        </table>
                    <a href="rooms.php">Atpakaļ</a> 
        </div>
</div>
    
</body>
</html>