<?php
require 'databasecontroll.php';
require 'functions.ini.php';
session_start();
class Users
{
    //https://stackoverflow.com/questions/4707053/php-user-class-login-logout-signup
    //https://www.c-sharpcorner.com/UploadFile/0870a0/registration-and-login-form-in-php-using-oop/

    private $role = 0;
    private $salt = 'afaf12e2';
    private $db;       // stores the database handler
  
    function __construct($conn) {  
        $this->db = $conn;  
    } 
    public function register($username,$email,$pwd,$pwdRepeat)
    {
        if(emptyInput($username, $email, $pwd,$pwdRepeat)){
            header("location: ../signup.php?error=emptyfields&username=$username&email=$email");
             exit();
         }elseif (invalidUsername($username)) {
             header("location: ../signup.php?error=username&username=$username&email=$email");
             exit();
         }elseif (invalidEmail($email)) {
             header("location: ../signup.php?error=email&username=$username&email=$email");
             exit();
         }elseif(pwdTest($pwd)){
             header("location: ../signup.php?error=password&username=$username&email=$email");
             exit();
         }elseif(badPwd($pwd)){
            header("location: ../signup.php?error=badpassword&username=$username&email=$email");
            exit();
        }elseif(pwdMatch($pwd,$pwdRepeat)){
             header("location: ../signup.php?error=nomatch&username=$username&email=$email");
             exit();
         }else{          
            $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ? or email=?');
            $stmt->execute(array($username,$email));
         }

        if ($stmt->rowCount() == 0) {
        $hash = password_hash($pwd, PASSWORD_DEFAULT);
        //katram lietotājam unikāla id piešķiršana
        $id = uniqidReal();
            $user_list = $this->db->prepare('SELECT * FROM users');
            $stmt->execute();
            //pārbaude, lai id tiešām būtu unikāls(nav pārāk nepieciešams, bet var noderēt, ja lietotāju skaits būtu mērāms
            // simtos miljonu)
            while ($ids = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                if ($id == $ids['userID'])
                {
                    $id = uniqidReal();
                }
            }
        $stmt = $this->db->prepare('INSERT INTO users (userID,username, email, password, role,salt) VALUES (?,?,?,?,?,?)');
        $stmt->execute([$id,$username, $email, $hash, $this->role, $this->salt]);
        return true;
        } else {
            //tikai email
            header("location: ../signup.php?error=taken&username=$username&email=$email");
            exit();
            }
    }
    public function login($name_email,$pwd)
    {
        if(empty($name_email) || empty($pwd)){
            header("location: ../index.php?error=empty&name_email=$name_email");
            exit();
        }
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ? or email=?');
        $stmt->execute(array($name_email,$name_email));
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($pwd, $user['password'])) {
                $stmt = $this->db->prepare('UPDATE users set status = "1" where userID=?');
                $stmt->execute([$user['userID']]);
                $_SESSION['userID'] = $user['userID'];
                return true;
            }else{
                header("location: ../index.php?error=wrong&name_email=$name_email");
                exit();
            }
        } else{
            header("location: ../index.php?error=wrong&name_email=$name_email");
            exit();
        }
    }
    public function logout()
    {
        # code...
    }
}
