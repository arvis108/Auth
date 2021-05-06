<?php
require_once 'includes/config.php';
include 'includes/google-callback.php';
if (isset($_SESSION['userName'])) {
header("location:rooms.php");
}

$fb = new Facebook\Facebook([
    'app_id' => FACEBOOK_CLIENT_ID,
    'app_secret' => FACEBOOK_CLIENT_SECRET,
    'default_graph_version' => 'v3.2',
    ]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; // Optional permissions
$callbackUrl = htmlspecialchars('http://localhost/Auth/includes/fb-callback.php');
$loginUrl = $helper->getLoginUrl($callbackUrl, $permissions);
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
                    <a href="<?php echo filter_var($loginUrl,FILTER_SANITIZE_URL); ?>" class="social"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?php echo filter_var($login_url, FILTER_SANITIZE_URL);?>" class="social"><i class="fab fa-google-plus-g"></i></a>
                </div>
                <span>vai aizpildi laukus</span>
                <input type="text" name="name_email" value="<?php echo isset($_GET['name_email']) ? htmlspecialchars($_GET['name_email']) : ''; ?>" placeholder="E-pasts/Lietotājvārds" required/>
                <div class="pwd_div"><input type="password" name = "password" id="loginPwd" placeholder="Parole" required/>
                <i class="fas fa-eye" id="loginEye"></i></div>
                <input type="submit" value="Ienākt" name="psubmit">
                <a href="forgot_password.php" class="forgot">Aizmirsi paroli?</a>
                <?php 
            if (isset($_GET['error'])) {
                switch ($_GET['error']) {
                    case 'none':
                        echo '<p>Reģistrēšanās bija veiksmīga! Varat pieslēgties!</p>';
                        break;                               
                    case 'empty':
                        echo '<p>Aizpildiet visus laukus!</p>';
                        break;                                
                    case 'emailnotvalidated':
                        echo '<p>Jūsu E-pasts nav verificēts! Lūdzu apskatieties savu E-pastu.</p>';
                        break;                                
                    case 'toomanyattempts':
                        echo '<p>Tika veikti pārāk daudzi neveiksmīgi mēģinājumi lūdzu uzgaidiet.</p>';
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