<?php 
require 'includes/databasecontroll.php';
if(isset($_SESSION["userID"])) 
{
    header("location:room.php"); 
}
if(!isset($_GET["token"])) 
{
    header("location:index.php"); 
}

$stmt = $conn->prepare("SELECT * FROM users WHERE email_token = ?");
$stmt->execute([$_GET["token"]]);
if ($stmt->rowCount() == 0){
    header("location:index.php"); 
}
?> 

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <title>Password Reset</title>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container"> 
        <?php
            if(isset($_GET['token'])){
                $token= $_GET['token'];
                }
                    //https://www.php.net/manual/en/datetime.diff.php#:~:text=As%20of%20PHP%205.2.2,%20DateTime%20objects%20can%20be,above%20example%20will%20output:%20bool(false)%20bool(true)%20bool(false)%20.
                    //lai pārbaudītu, vai tokens nav novecojis
                    $stmt = $conn->prepare("SELECT * FROM users WHERE email_token= ?");
                    $stmt->execute([$token]);
                    if ($stmt->rowCount() > 0){
                            $res = $stmt->fetch(PDO::FETCH_ASSOC);
                            $email= $res['email']; 

                            $stmt = $conn->prepare("UPDATE users SET  verified = 1 WHERE email = ?");

                            if( $stmt->execute([$email])) { 
                                echo " <form>
                                <h1> Jūsu E-pasts tika verificēts veiksmīgi.<h1> 
                                <a href='index.php?email=verified'>Klikšķini šeit, lai ielogotos!</a> 
                                </form>
                                ";
                    }
                        } else {
                            echo " <form>
                                <h1> E-pasta aktivizēšana ir jāpaveic 1h laikā<h1> 
                                <br></br>
                                <h4> Lūdzu mēģiniet reģistrēties atkārtoti.<h1>  
                                </form>
                        ";
                        }                     

         
    
    ?>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel">
                    <h1>Ienāc/Reģistrējies</h1>
                    <p class="main_p">Tev jau ir profils mūsu lapā?</p>
                    <a href="index.php">Ienāc</a>
                    <p class="main_p">Vai arī pievienojies mums tagad!</p>
                    <a href="signup.php">Reģistrējies</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>