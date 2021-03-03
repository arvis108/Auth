<?php
require './includes/fb_init.php';
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
            <form action="includes/signin.inc.php" method="POST">
           
            
                <h1>Ielogojies ar</h1>
                <div class="social-container">
                    <a href="<?php echo $login_url; ?>" class="social"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                    <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <span>vai aizpildi laukus</span>
                <input type="text" name="name_email" value="<?php echo isset($_GET['name_email']) ? $_GET['name_email'] : ''; ?>" placeholder="E-pasts/Lietotājvārds" required/>
                <input type="password" name = "password" id="loginPwd" placeholder="Parole" required/>
                <i class="fas fa-eye" id="loginEye"></i>
                <input type="submit" value="Ienākt" name="psubmit">
                <a href="#" class="forgot">Aizmirsi paroli?</a>
                <?php 
            if (isset($_GET['error'])) {
                switch ($_GET['error']) {
                    case 'none':
                        echo '<p>Reģistrēšanās bija veiksmīga! Varat pieslēgties!</p>';
                        break;
                    case 'unknown':
                        echo '<p>Nepareizs lietotājvārds un/vai parole!</p>';
                        break;
                    case 'wrong':
                        echo '<p>Nepareizs lietotājvārds un/vai parole!</p>';
                        break;                                
                    case 'empty':
                        echo '<p>Aizpildiet visus laukus!</p>';
                        break;                                
                    default:
                    echo '<p>Nepareizs lietotājvārds un/vai parole!</p>';
                        break;
                }              
            }
            ?>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel">
                    <h1>Sveiks!</h1>
                    <p class="main_p">Tev vēljoprojām nav piekļuves mūsu lapai? Ievadi pāris datus par sevi un pievienojies mums!</p>
                    <a href="signup.php">Reģistrējies</a>
                </div>
            </div>
        </div>
    </div>
<script>

const pswrdField = document.getElementById("loginPwd"),
toggleIcon = document.getElementById("loginEye");

toggleIcon.onclick = () =>{
  if(pswrdField.type === "password"){
    pswrdField.type = "text";
    toggleIcon.classList.add("active");
  }else{
    pswrdField.type = "password";
    toggleIcon.classList.remove("active");
  }
}

</script>
</body>

</html>