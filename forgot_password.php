<?php 

if(isset($_SESSION["userID"])) 
{
    header("location:room.php"); 
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <script src="https://kit.fontawesome.com/60bfce93ff.js" crossorigin="anonymous"></script>
    <title>Document</title>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container"> 

         <form action="includes/forgot.inc.php" method="POST">
                <h1>Aizmirsi paroli?</h1>
                <label >E-pasts vai lietotājvārds</label>
                <input type="text" name="login_var" value="<?php if(!empty($err)){ echo  $err; } ?>" required/>
                <input type="submit" value="Atjaunot paroli" name="subforgot">
            <?php if(isset($_GET["err"])){
                $err=$_GET["err"];
                echo "<p class='errmsg'>Lietotājvārds/E-pasts neeksistē</p>";
               } 
               // If server error 
               if(isset($_GET["servererr"])){ 
               echo "<p class='errmsg'>Neizdevās aizsūtīt ziņu. Lūdzu mēģiniet vēlāk.</p>";
                  }
                  //if other issues 
                  if(isset($_GET["somethingwrong"])){ 
                echo "<p class='errmsg'>Radās kļūme.</p>";
                  }
               // If Success | Link sent 
               if(isset($_GET["sent"])){
               echo "<div class='successmsg'>Vēstule ar paroles atjaunošanas hipersaiti veiksmīgi tika aizsūtīta uz jūsu E-pastu. Tas var aizņemt pāris minūtes.</div>"; 
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