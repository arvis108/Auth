<?php
require_once 'includes/config.php';
//tiklīdz lietotājs vēlēsies veikt reģistrāciju
//datu bāzē tiks izdzēsti visi lietotāji, kuri 1h laikā nebūs verificējuši savu E-pastu
//nepieciešams gadījumā, ja kāds speciāli ir veicis reģistrāciju ar ne savu E-pastu
checkEmailValidation($conn);
if (isset($_SESSION['userName'])) {
    header("location:rooms.php");
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" integrity="sha384-vSIIfh2YWi9wW0r9iZe7RJPrKwp6bG+s9QZMoITbCckVJqGCCRhc+ccxNcdpHuYu" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>
    <title>Document</title>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container">
        
            <form action="includes/signup.inc.php" method="POST">
                <h1>Reģistrēties</h1>
                <div class="bloks">
                <input type="text"  class = "name" name="username" placeholder="Lietotājvārds" value="<?php echo isset($_GET['username']) ? htmlspecialchars($_GET['username']): ''; ?>" required/>
                <span class="tooltiptext"><ul><li>Lietotājvārds var sastāvēt no burtiem,cipariem un .-_ simboliem!</li>
                <li>Minimālais garums 8 simboli!</li> </ul></span>    
                </div>
                <p  class="emsg hidden">Minimālais garums 8 simboli</p>
                <p  class="emsg hidden">Atļauts izmantot burtus,ciparus un ._- simbolus</p>
                <div class="bloks">
                <input type="email" name="email" placeholder="E-pasts" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" required/>       
            </div>
                <div class="bloks">
                <div class="pwd_div"><input type="password" class = "pwd" id="pwdInput" name="pwd" placeholder="Parole" required/>
                <i class="fas fa-eye" id="loginEye"></i></div>
                <span class="tooltiptext">Minimālais garums 8 simboli!</span>
                <progress max="4" id="password-strength-meter"></progress>
                <p id="password-strength-text"></p>
                </div>
                <p  class="emsgp hidden">Minimālais garums 8 simboli</p>
                <p  class="emsgmissmatch hidden">Paroles nesakrīt</p>
                <div class="bloks">
                <div class="pwd_div"><input type="password" name="pwdRepeat" id="pwdrInput" class = "pwdR" placeholder="Atkārtota parole" required/>
                <i class="fas fa-eye" id="loginEyeRepeat"></i></div>
                </div>
                <div class="cb_bloks">
                    <input type="checkbox" class="cb" name="term_cb">     
                    <span>Es piekrītu <a href="#">Lietošanas noteikumiem</a> un <a href="#">Privātuma politikai </a></span>
                </div>
                <input type="submit" name="rsubmit" value="Reģistrēties">
            <?php
                if(isset($_SESSION['errors'])){
                $array = $_SESSION['errors'];
                foreach($array as $val) {
                    echo '<p class="error_p">'.htmlspecialchars($val).'<Br></p>';
                }
                unset($_SESSION['errors']);
            } 
            if (isset($_GET['error'])){
                switch ($_GET['error']) {
                    case 'mail':
                        echo '<p>Radās kļūda sūtot E-pasta ziņu!</p>';
                        break;
                    case 'emailtaken':
                        echo '<p>E-pasts ir aizņemts!</p>';
                        break;
                    case 'usernametaken':
                        echo '<p>Lietotājvārds ir aizņemts!</p>';
                        break;
                    default:
                        break;
                }
            }
            ?>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel">
                <?php  if(isset($_GET['error']) && $_GET['error'] == 'none'){
                        echo '<h3>Lūdzu apstipriniet savu E-pasta adresi!</h3>';
                        echo '<br>';
                        echo '<h4>Uz E-pasta adresi ar kuru jūs reģistrējāties tika aizsūtīta lietotāja profila aktivizācijas hipersaite.</h4>';
                        echo '<br>';
                        echo '<p>Tikai pēc veiksmīgas aktivizācijas jums būs piekļuve savam profilam.</p>';
                    } else{
                        echo '<h1>Tev jau ir konts mūsu lapā?</h1>
                        <a href="index.php">Pieslēdzies</a>';
                    }
                    
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        var strength = {
        0: "Ļoti vāja",
        1: "Slikta",
        2: "Vāja",
        3: "Apmierinoša",
        4: "Stipra"
    }

var password = document.getElementById('pwdInput');
var meter = document.getElementById('password-strength-meter');
var text = document.getElementById('password-strength-text');

password.addEventListener('input', function() {
    var val = password.value;
    var result = zxcvbn(val);

  // Update the password strength meter
    meter.value = result.score;

  // Update the text indicator
    if (val !== "") {
    text.innerHTML = "Paroles stiprums: " + strength[result.score]; 
    } else {
    text.innerHTML = "";
    }
});
            $(document).ready(function(){
            var $regexname=/^([a-zA-Z0-9_.-]{8,255})$/;
            $('.name').on('keypress keydown keyup',function(){
                    if (!$(this).val().match($regexname)) {
                    // there is a mismatch, hence show the error message
                        $('.emsg').removeClass('hidden');
                        $('.emsg').show();
                    }else{
                        // else, do not display message
                        $('.emsg').addClass('hidden');
                    }
                });

            $('.pwd').on('keypress keydown keyup',function(){
                var value = $(this).val();
                    if (value.length < 8) {
                    // there is a mismatch, hence show the error message
                        $('.emsgp').removeClass('hidden');
                        $('.emsgp').show();
                    }else{
                        // else, do not display message
                        $('.emsgp').addClass('hidden');
                    }
                });
            $('.pwdR').on('keypress keydown keyup',function(){
                var value = $(this).val();
                var password = $(".pwd").val();

            if (value != password) {
                $('.emsgmissmatch').removeClass('hidden');
                        $('.emsgmissmatch').show();
            }
            else{
                        // else, do not display message
                        $('.emsgmissmatch').addClass('hidden');
                    }
                });
            });

const pswrdField = document.getElementById("pwdInput"),
pswrdField2 = document.getElementById("pwdrInput"),
toggleIcon = document.querySelector(".bloks i"),
toggleIcon2 = document.getElementById("loginEyeRepeat");

toggleIcon.onclick = () =>{
  if(pswrdField.type === "password"){
    pswrdField.type = "text";
    toggleIcon.classList.add("active");
  }else{
    pswrdField.type = "password";
    toggleIcon.classList.remove("active");
  }
}
toggleIcon2.onclick = () =>{
  if(pswrdField2.type === "password"){
    pswrdField2.type = "text";
    toggleIcon2.classList.add("active");
  }else{
    pswrdField2.type = "password";
    toggleIcon2.classList.remove("active");
  }
}
    </script>
</body>

</html>