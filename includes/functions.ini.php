<?php
require 'C:\laragon\www\Auth\vendor\autoload.php';
use function Composer\Autoload\includeFile;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\laragon\www\Auth\vendor\phpmailer\phpmailer\src\Exception.php';
require 'C:\laragon\www\Auth\vendor\phpmailer\phpmailer\src\PHPMailer.php';
//edit email username and password
define ('GUSER','');
define ('GPWD','');

https://github.com/PHPMailer/PHPMailer
function smtpmailer($to, $from, $from_name, $subject, $body) { 
    //https://stackoverflow.com/questions/22927634/smtp-connect-failed-phpmailer-php
    global $error;
    $mail = new PHPMailer();  // create a new object
    $mail->IsSMTP(); // enable SMTP
    $mail->SMTPDebug = 2;  // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true;  // authentication enabled
    $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for GMail
    $mail->SMTPAutoTLS = false;
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';

    $mail->Username = GUSER;  
    $mail->Password = GPWD;           
    $mail->SetFrom($from, $from_name);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);
    if(!$mail->Send()) {
        $error = 'Mail error: '.$mail->ErrorInfo; 
        return false;
    } else {
        $error = 'Message sent!';
        return true;
    }
}
function emptyInput($username, $email, $pwd, $pwdRepeat)
{
    return (empty($username) || empty($email) || empty($pwd) || empty($pwdRepeat));   
}

function invalidUsername($username)
{
    return (!preg_match("/^[a-zA-Z0-9_.-]{8,31}$/", $username));
}

function invalidEmail($email)
{
    return (!filter_var($email, FILTER_VALIDATE_EMAIL));
}
function pwdMatch($pwd, $pwdRepeat)
{
   return ($pwd !== $pwdRepeat);
}
function pwdTest($pwd)
{
    return (strlen($pwd) < 8 || strlen($pwd) > 64); 
}
function pwdCharTest($pwd)
{
    return (preg_match('/(\w)\1{3,}/', $pwd));
}
function pwdUsernameTest($username, $email, $pwd)
{
    $prefix = substr($email, 0, strrpos($email, '@'));
    return ($username == $pwd || $prefix == $pwd );
}

function badPwd($pwd)
{
    $myFile = "C:\laragon\www\Auth\includes\pwd_black_list.txt";
        $fh = fopen($myFile, "r");
        if ($fh) {
        while ( !feof($fh) ) {
            $passwords[] = trim(fgets($fh));    
            }
        }
    return (in_array($pwd, $passwords,FALSE));
}
//https://www.php.net/uniqid
function uniqidReal($lenght = 13) {
    // uniqid gives 13 chars, but you could adjust it to your needs.
    if (function_exists("random_bytes")) {
        $bytes = random_bytes(ceil($lenght / 2));
    } elseif (function_exists("openssl_random_pseudo_bytes")) {
        $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    } else {
        throw new Exception("no cryptographically secure random function available");
    }
    return substr(bin2hex($bytes), 0, $lenght);
}

function checkLoginState($conn,$username,$pwd)
{
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user['provider'] !=''){
            //ja tiek izmantots single sign on, tad parole netiek saglabāta, tāpēc tā nav jāpārbauda
            return true;
        } else if($user['password']== $pwd){ 
                return true; 
        } else{
            return false;
        }
        
    }
    return false;
}

function checkEmailValidation($conn)
{
    //funkcija, kas izdzēš lietotāja datus no datu bāzes, ja e-pasts nav aktivizēts 1h laikā
    $stmt = $conn->prepare('SELECT * FROM users WHERE verified = 0');
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $origin = new DateTime("now");
            $target = new DateTime($user['date_created']);
            $interval = date_diff($origin, $target);
            if(($interval->format('%r%h.%i')) < -1){
                $stmt = $conn->prepare('DELETE FROM users WHERE verified = 0');
                $stmt->execute();
                } 
}
}
function user_log($db,$user_id,$successfullyLogged)
    {

        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $uip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $uip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $uip = $_SERVER['REMOTE_ADDR'];
        }
        $stmt = $db->prepare('INSERT INTO logs (fk_userID_logs,userIP, successfullyLogged) VALUES (?,?,?)');
        $stmt->execute(array($user_id,$uip,$successfullyLogged));
    }
function attemptControll($db,$user_id)
    {
        $currentDatetime = date('Y-m-d H:i:s',time()-60*10);
        $stmt = $db->prepare('SELECT * FROM logs WHERE fk_userID_logs = ? AND loginStartTime > ? and successfullyLogged = 0');
        $stmt->execute(array($user_id,$currentDatetime));
        if ($stmt->rowCount() > 10) {
            return true;
        }
        return false;
    }
function deleteUser($db,$user_id)
    {
        $stmt = $db->prepare('DELETE FROM users WHERE userID = :id');
        $stmt->bindValue(':id', $user_id);
        $stmt->execute(); 
        return true; 
    }
        

