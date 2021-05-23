<?php
require_once 'includes/config.php';
if(isset($_POST['delBtn']) && $_SESSION['token2'] == $_POST['del_token'] ) {
    $pwd = $_POST['del_pwd'];
    $stmt = $conn->prepare('SELECT * FROM users WHERE userID = ?');
    $stmt->execute(array($_SESSION['userID']));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $pwd_peppered = hash_hmac("sha256", $pwd, PEPPER);
    if (password_verify($pwd_peppered, $user['password'])) {
        if(deleteUser($conn,$_SESSION['userID'])){
            session_unset();
            session_destroy();
            header("Location: index.php");
        }
}
}
if(!isset($_POST['delete'])){
    header("location: rooms.php?error=access");
}
$token_value = uniqidReal();
$_SESSION['token2'] = $token_value;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/delete_styles.css">
    <title>Delete</title>
</head>
<body>
    <div class="block">
    <h1>Vai Jūs esat pārliecināts, ka vēlaties slēgt savu kontu?</h1>
    <h3>Visi ar Jums saistītie dati tiks izdzēsti!</h3>
    <form action="" method="POST">
    <input type="hidden" name="del_token" value="<?php echo $token_value?>" />
    <input type="password" name="del_pwd" placeholder="Konta parole:" />
    <input type="submit" value="Slēgt kontu" name="delBtn" id="delete_btn" onclick="return confirm('Vai jūs esat drošs?');">
    </form>
    <a href="rooms.php">Atcelt</a>
    </div>
</body>

</html>