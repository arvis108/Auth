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

$stmt = $conn->prepare("SELECT * FROM password_reset WHERE token = ?");
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
            //form for submit 
                if(isset($_POST['sub_set'])){
                    $password = $_POST['password'];
                    $passwordConfirm = $_POST['passwordConfirm'];
                    //https://www.allphptricks.com/forgot-password-recovery-reset-using-php-and-mysql/#:~:text=%20Steps%20to%20Forgot%20Password%20Recovery%20(Reset)%20using,that...%204%20Create%20a%20CSS%20File%20More
                    //https://www.php.net/manual/en/datetime.diff.php#:~:text=As%20of%20PHP%205.2.2,%20DateTime%20objects%20can%20be,above%20example%20will%20output:%20bool(false)%20bool(true)%20bool(false)%20.
                    //lai pārbaudītu, vai tokens nav novecojis
                    $origin = new DateTime("now");
                        if($password ==''){
                            $error[] = 'Parole netika ievadīta';
                        }
                        if($passwordConfirm ==''){
                            $error[] = 'Atkārtojiet paroli';
                        }
                        if($password != $passwordConfirm){
                            $error[] = 'Paroles nesakrīt';
                        }
                        if(strlen($password)<8){ // min 
                        $error[] = 'Paroles minimālais garums ir 8 simboli';
                        }
                        if(strlen($password)>255){ // Max 
                        $error[] = 'Maksimālais garums 255 simboli';
                        }
                        
                    $stmt = $conn->prepare("SELECT * FROM password_reset WHERE token= ?");
                    $stmt->execute([$token]);
                    if ($stmt->rowCount() > 0){
                            $res = $stmt->fetch(PDO::FETCH_ASSOC);
                            $email= $res['email']; 
                            $target = new DateTime($res['time']);
                        }
                        $interval = date_diff($origin, $target);
                        //ja tokens eksistē jau vairāk nekā 1h, neatļaut atjaunot paroli
                        if(($interval->format('%r%h.%i')) < -1){
                            $error[] = 'Link has been expired more than 1h passed ';
                        }
                        if(isset($email) != '' ) {
                            $emailtok=$email;
                            }
                        else 
                            { 
                            $error[] = 'Link has been expired or something missing ';
                        }
            if(!isset($error)){
            $options = array("cost"=>4); //cost can be lower, because there is no threats
            $password = password_hash($password,PASSWORD_BCRYPT,$options);
            $stmt = $conn->prepare("UPDATE users SET  password = ? WHERE email = ?");

            if( $stmt->execute([$password,$emailtok])) { 
                $success="<div class='successmsg'>
                <br> Your password has been updated successfully.. <br> 
                <a href='index.php'>Login here... </a> 
                </div>";
                $stmt = $conn->prepare("DELETE from password_reset WHERE token = ?");
                $stmt->execute([$token]);
    }
} 
    }
    ?>
        <form method="POST" action="">
                <h1>Atjaunot paroli</h1>
                <input type="password" name = "password" placeholder="Jaunā parole" required/>
                <input type="password" name = "passwordConfirm" placeholder="Atkārtota parole" required/>
                <input type="submit" value="Atjaunot paroli" name="sub_set">
                <?php 
                    if(isset($error)){
                            foreach($error as $error){
                                echo '<div class="errmsg">'.$error.'</div><br>';
                            }
                        }
                        if(isset($success)){
                        echo $success;
                    }
                ?>
            </form>
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