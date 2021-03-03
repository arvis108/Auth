<?php

use function Composer\Autoload\includeFile;

function emptyInput($username, $email, $pwd, $pwdRepeat)
{
    return (empty($username) || empty($email) || empty($pwd) || empty($pwdRepeat));   
}

function invalidUsername($username)
{
    return (!preg_match("/^[a-zA-Z0-9_.-]*$/", $username));
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
    return (strlen($pwd) < 8); 
}

function badPwd($pwd)
{
    $myFile = "pwd-list.txt";
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

function checkLogin($conn,$id)
{
    $stmt = $conn->prepare('SELECT userID FROM users WHERE userID=?');
    $stmt->execute([$id]);
    if ($stmt->rowCount() > 0) {
        return true;
    }
    return false;
}
function logout($conn,$ID)
    {
        $stmt = $conn->prepare('UPDATE users set status = "0" where userID=?');
        $stmt->execute([$ID]);
    }