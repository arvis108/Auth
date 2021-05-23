<?php 
require_once 'includes/config.php';

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
    <meta name="referrer" content="origin-when-crossorigin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/room_styles.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.2/css/all.css" integrity="sha384-vSIIfh2YWi9wW0r9iZe7RJPrKwp6bG+s9QZMoITbCckVJqGCCRhc+ccxNcdpHuYu" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Čata istabas</title>
</head>
<body>
<nav>
        <h1>Sveicināts <?php echo $_SESSION['userName'];?>!</h1>
        <div class="flex_forms">
            <button onclick="togglewiev('user_data');">Mani dati</button>
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

            <div id = "user_data">
                    <form action="includes/changr_username.php" method="POST" class="profile" referrerpolicy="origin">
                    <div class="bloks">
                        <label for="username">Lietotājvārda maiņa</label>
                        <input type="text" name="lietotajvards" id="username" value="<?php echo isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';?>">
                        <input type="hidden" name="username_token" value="<?php echo $token_value;?>" />
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
                    <div class="line"></div>
                    <p class="error_msg">Brīdinām! Pēc paroles maiņas jums būs jāpieslēdzas atkārtoti!</p>

                        <form action="includes/change_pwd.php" method="POST" class="profile">
                            <div class="pbloks">
                                <div class="pwd_div">
                                    <input type="password" id="pwdInput" name="oldpwd" placeholder="Vecā parole" required/>
                                    <i class="fas fa-eye" id="loginEye" onclick="showPwd('pwdInput','loginEye')"></i>
                                </div>                                
                            </div>

                            <div class="pbloks">
                                <div class="pwd_div">
                                    <input type="password" name="newpwd" id="newpwdInput" placeholder="Jaunā parole"  oninput="pwdmeter('newpwdInput','password-strength-meter','password-strength-text'); lenghtCheck('newpwdInput','pwdlengthError')" required/>
                                    <i class="fas fa-eye" id="loginEyeNew" onclick="showPwd('newpwdInput','loginEyeNew')"></i>
                                </div>
                            </div>

                            <div class="pbloks">
                            <progress max="4" id="password-strength-meter"></progress>
                                    <p id="password-strength-text"></p>
                            </div>
                            
                            <p class="emsgmissmatch hidden" id="pwdlengthError">Minimālais garums 8 simboli</p>

                            <div class="pbloks">
                                <div class="pwd_div">
                                    <input type="password" name="newpwdr" id="newpwdInputr" placeholder="Atkārtota jaunā parole"  oninput="checkPassword('newpwdInput','newpwdInputr','emsgmissmatch')" required/>
                                    <i class="fas fa-eye" id="loginEyeRepeat" onclick="showPwd('newpwdInputr','loginEyeRepeat')"></i>
                                </div>
                            </div>

                            <p  class="emsgmissmatch hidden" id="emsgmissmatch">Paroles nesakrīt</p>
                            <input type="hidden" name="pwd_token" value="<?php echo $token_value;?>" />
                            <input type="submit" class="submit_btn" value="Mainīt paroli" name="parole_submit">
                        </form>
                            <?php
                            if(isset($_SESSION['errors'])){
                                $array = $_SESSION['errors'];
                                foreach($array as $val) {
                                    echo '<p class="error_msg">'.htmlspecialchars(trim($val)).'<Br></p>';
                                }
                                unset($_SESSION['errors']);
                            }
                            if(isset($_GET['errors']) && $_GET['error'] == 'wrong_pwd'){
                                echo '<p class="error_msg">Tika ievadīta nepareiza parole<Br></p>';
                            }
                            
                            ?>
                            <div class="line"></div>
                            <p class="error_msg">Brīdinām! Slēdzot kontu visi jūsu dati tiks izdzēsti!</p>
                        <form action="delete.php" method="POST" class="delete_form">
                            <input type="submit" class="delete" value="Slēgt kontu" name="delete">
                        </form>
                        <div class="line"></div>
                </div>
            <h3 class="online">Lietotāji tiešsaistē</h3>
            <ul>
                <?php 
                $stmt = $conn->prepare("SELECT username FROM users where status = '1'");
                $stmt->execute();
                while ($users = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    foreach ($users as $value) {
                        //jāuztaisa, lai admin nevar sevi nobanot
                        if($_SESSION['role']== 1){
                            echo '<li> <p class = "list_username">'.htmlspecialchars($value).
                            '</p><form method="POST" action ="includes/bad_user.php/?username='.$value.'&action=mute">
                            <input type="submit" name="mute" value="Apklusināt">
                            </form>
                            <form metho="POST" action ="includes/bad_user.php/?username='.$value.'&action=ban">
                            <input type="submit" name="ban" value="Nobanot">
                            </form></li>';
                        }else {
                            echo '<li> <p class = "list_username">'.htmlspecialchars($value).
                            '</p></li>';
                        }  
                    }
                }
                ?>
        </ul>
    </div>
    
    </div>
    </div>
    <?php 
    if(isset($_GET['data']) && $_GET['data'] == 'show'){
        echo "
        <script>
        var view = document.getElementById('user_data');
        if(view.style.display == 'flex'){
            view.style.display = 'none';
        } else{
            view.style.display = 'flex';
        }
        </script>
        ";
    }
    
    ?>
    <script src="JavaScript/js.js" type="text/javascript">
    </script>

<script>
$(document).ready(function(){
<?php
if(isset($_SESSION["userID"]))
{
?>
    function update_user_activity(){
    var action = 'update_time';
        $.ajax({
            url:"includes/user_statuss.php",
            method:"POST",
            data:{action:action},
            success:function(data){
                }
        });
    }
    update_user_activity();
    setInterval(function(){ 
        update_user_activity();
    }, 3000);

<?php
}
else
{
?>
fetch_user_login_data();

setInterval(function(){
        fetch_user_login_data();
    }, 3000);

    function fetch_user_login_data(){
        var action = "fetch_data";
        $.ajax({
        url:"includes/user_statuss.php",
        method:"POST",
        data:{action:action},
        success:function(data)
        {
        }
        });
    }
    <?php
}
?>
});
</script>
</body>

</html>