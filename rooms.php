<?php 
session_start();
require 'includes/functions.ini.php';
require 'includes/databasecontroll.php';

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    include './includes/logout.inc.php';
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
//ja lietotājs nav ielogojies
//vai arī norādītai lietotāja sesijas identifikators neeksisē
//tiek aizsūtīts uz sākuma lapu
if(!isset($_SESSION['userName']) || !checkLoginState($conn,$_SESSION['userName'],$_SESSION['pwd']) ){
    header("location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/room_styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" integrity="sha384-vSIIfh2YWi9wW0r9iZe7RJPrKwp6bG+s9QZMoITbCckVJqGCCRhc+ccxNcdpHuYu" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
    <title>Document</title>
</head>
<body>
<nav>
        <h1>Sveicināts <?php echo $_SESSION['userName'];?>!</h1>
        <div class="flex_forms">
            <button onclick="togglewiev();">Mani dati</button>
            <form action="./includes/logout.inc.php" method="POST" class="logout">
                <input type="submit" name="logout" value="Log-Out" class="logout_btn effect01">
            </form>
        </div>
</nav>
<div class="wrapper">
    <div class="room">
    <?php 
    $stmt = $conn->prepare('SELECT name,foto FROM chatRooms');
    $stmt->execute();
    while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rooms[] = $res;
    }

        foreach ($rooms as $key => $value) {
            echo '<form class="rooms_form" action="chat.php?room='.htmlspecialchars($rooms[$key]["name"]).'" method="POST">';
            echo '<p>'.htmlspecialchars($rooms[$key]["name"]).'</p>';
            echo '<input type="submit" class="btn effect01" name="roomSubmit" value="Pievienoties">';
            echo '</form>';
            echo '<img src="img/'.htmlspecialchars($rooms[$key]["foto"]).'">';
        }
    

    if(isset($_SESSION['role']) && $_SESSION['role']== 1){
        echo '<form action="pievienot.php" method="POST" class = "pievienot_form">';
            echo '<input type="submit" class="btn effect01" name="pievienotSubmit" value="Pievienot">';
            echo '</form>';
    }
    
    ?>
    
    </div>
    <div class="right">
        <div class="active_users">
            <?php 
            $token_value = uniqidReal();
            $_SESSION['token1'] = $token_value;?>

            <div class="colum" id = "user_data">

            <form action="includes/changr_username.php" method="POST" class="profile">
            <div class="bloks">
                <label for="username">Lietotājvārda maiņa</label>
                <input type="text" name="lietotajvards" id="username" value="<?php echo isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';?>">
                <span class="tooltiptext"><ul><li>Lietotājvārds var sastāvēt no burtiem,cipariem un .-_ simboliem!</li>
                <li>Minimālais garums 8 simboli!</li> </ul></span>
            </div>
                <input type="submit" class="submit_btn" value="Mainīt lietotājvārdu" name="lietotajvarda_submit">
            </form>
            <?php 
            if (isset($_GET['error'])) {
                echo '<p class="error_msg">';
                switch ($_GET['error']) {
                    case 'taken':
                        echo 'Lietotājvārds ir aizņemts.';
                        break;
                    case 'bad_username':
                        echo 'Lietotājvārds var saturēt ciparus,burtus un .-_ simbolus, minimālais garums 8 simboli.';
                        break;
                    case 'changed':
                        echo 'Lietotājvārds veiksmīgi tika pamainīts.';
                        break;
                    default:
                        break;
                }
                echo '</p>';
            }
            ?>
                <form action="includes/change_pwd.php" method="POST" class="profile">
                    
                    <div class="pbloks">
                        <div class="pwd_div">
                            <input type="password" class = "pwd" id="pwdInput" name="oldpwd" placeholder="Vecā parole" required/>
                            <i class="fas fa-eye" id="loginEye"></i>
                        </div>
                        
                    </div>

                    <div class="pbloks">
                        <div class="pwd_div">
                            <input type="password" name="newpwd" id="pwdrInput" class = "pwdR" placeholder="Jaunā parole" required/>
                            <i class="fas fa-eye" id="loginEyeRepeat"></i>
                            <progress max="4" id="password-strength-meter"></progress>
                            <p id="password-strength-text"></p>
                        </div>
                    </div>

                    <div class="pbloks">
                        <div class="pwd_div">
                            <input type="password" name="pwdnewrpt" id="pwdrInput" class = "pwdR" placeholder="Atkārtota jaunā parole" required/>
                            <i class="fas fa-eye" id="loginEyeRepeat"></i>
                        </div>
                    </div>

                    <p  class="emsgp hidden">Minimālais garums 8 simboli</p>
                    <p  class="emsgmissmatch hidden">Paroles nesakrīt</p>
                    <input type="hidden" name="pwd_token" value="<?php echo $token_value;?>" />
                    <input type="submit" class="submit_btn" value="Mainīt paroli" name="parole_submit">
                </form>
                    <?php
                    if(isset($_SESSION['errors'])){
                        $array = $_SESSION['errors'];
                        foreach($array as $val) {
                            echo '<p class="error_p">'.htmlspecialchars($val).'<Br></p>';
                        }
                        unset($_SESSION['errors']);
                    } ?>
                <form action="delete.php" method="POST" class="delete_form">
                    <input type="submit" class="delete" value="Slēgt kontu" name="delete">
                </form>

                    </div>
            <h3>Lietotāji tiešsaistē</h3>
            <ul>
                <?php 
                $stmt = $conn->prepare("SELECT username FROM users where status = '1'");
                $stmt->execute();
                while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    foreach ($users as $value) {
                        echo '<li data-icon="🦄">'.htmlspecialchars($value).'</li>';
                        if($_SESSION['role']== 1){
                            //jāuztaisa, lai admin nevar sevi nobanot
                            echo '
                            <form metho="POST" action ="">
                            <input type="submit" name="mute" value="Apklusināt">
                            </form>
                            <form metho="POST" action ="">
                            <input type="submit" name="ban" value="Nobanot">
                            </form>
                            ';
                        }     
                    }
                }
                ?>

        </ul>
    </div>
    
    </div>
    </div>
    <script>
    function togglewiev(params) {
        var view = document.getElementById('user_data');
        if(view.style.display == 'flex'){
            view.style.display = 'none';
        } else{
            view.style.display = 'flex';
        }
    }
    
    
    </script>
</body>

</html>