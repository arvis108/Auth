<?php
require 'includes/databasecontroll.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" integrity="sha384-vSIIfh2YWi9wW0r9iZe7RJPrKwp6bG+s9QZMoITbCckVJqGCCRhc+ccxNcdpHuYu" crossorigin="anonymous">
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
                <?php
                if (isset($_GET['error']) && ($_GET['error'] == 'username')) {
                    echo '<p>Lietotājvārdam jābūt vismaz 8 simbolu garam!</p>';
                }?>
                <div class="bloks">
                <input type="email" name="email" placeholder="E-pasts" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" required/>       
            </div>
            <?php
                if (isset($_GET['error']) && ($_GET['error'] == 'email')) {
                    echo '<p>Nepareizs E-pasts!</p>';
                } 
                    ?>
                <div class="bloks">
                <input type="password" class = "pwd" id="pwdInput" name="pwd" placeholder="Parole" required/>
                <i class="fas fa-eye"></i>
                <span class="tooltiptext">Minimālais garums 8 simboli!</span>
                </div>
                <p  class="emsgp hidden">Minimālais garums 8 simboli</p>
                <?php
                if (isset($_GET['error']) && ($_GET['error'] == 'password')) {
                    echo '<p>Parolei jābūt vismaz 8 simbolus garai!</p>';
                }elseif(isset($_GET['error']) && ($_GET['error'] == 'badpassword')){
                    echo '<p>Izvēlētā parole ir kompromitēta! Lūdzu izvēlieties drošāku paroli!</p>';
                }?>
                <div class="bloks">
                <input type="password" name="pwdRepeat" id="pwdrInput" class = "pwd" placeholder="Atkārtota parole" required/>
                <i class="fas fa-eye" id="eyeIcon"></i>
                </div>
                <input type="submit" name="rsubmit" value="Reģistrēties">
                <?php
                if (isset($_GET['error'])) {
                switch ($_GET['error']) {
                    case 'emptyfields':
                        echo '<p>Aizpildiet visus laukus!</p>';
                        break;                               
                    case 'nomatch':
                        echo '<p>Paroles nesakrīt!</p>';
                        break;                                
                    case 'taken':
                        echo '<p>Lietotājvārds un/vai E-pasts jau ir aizņemts!</p>';
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
                    <h1>Tev jau ir konts mūsu lapā?</h1>
                    <a href="index.php">Pieslēdzies</a>
                </div>
            </div>
        </div>
    </div>
    <script>
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
            });

const pswrdField = document.getElementById("pwdInput"),
pswrdField2 = document.getElementById("pwdrInput"),
toggleIcon = document.querySelector(".bloks i"),
toggleIcon2 = document.getElementById("eyeIcon");

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