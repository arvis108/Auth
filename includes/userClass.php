<?php
require_once 'databasecontroll.php';
require_once 'functions.ini.php';

class Users
{
    //https://stackoverflow.com/questions/4707053/php-user-class-login-logout-signup
    //https://www.c-sharpcorner.com/UploadFile/0870a0/registration-and-login-form-in-php-using-oop/
    private $role = 0;
    private $db;       // stores the database handler
    
    function __construct($conn) {  
        $this->db = $conn;  
    }

    function getUserID($name_email){
        $stmt = $this->db->prepare('SELECT userID FROM users WHERE username = ? or email=?');
        $stmt->execute(array($name_email,$name_email));

        return $stmt->fetchColumn();
    }
    public function register($username,$email,$pwd)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ? or email=?');
        $stmt->execute(array($username,$email));
        if ($stmt->rowCount() == 0) {
        $hash = password_hash($pwd, PASSWORD_DEFAULT);
                //katram lietotājam unikāla id piešķiršana
                    $id = uniqidReal();
                    $this->db->prepare('SELECT * FROM users');
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
                $token = bin2hex(random_bytes(50));
                $stmt = $this->db->prepare('INSERT INTO users (userID,username, email, password, role,email_token) VALUES (?,?,?,?,?,?)');
                $stmt->execute([$id,$username, $email, $hash, $this->role,$token]);
                $FromName="Auth Chat";
                $FromEmail= GUSER;
                $subject="E-pasta apstiprināšana"; 
                $output='<p>Cienījamais lietotāj!</p>';
                $output.='<p>Izmantojiet zemāk norādīto hipersaiti, lai apstiprinātu savu E-pasta paroli un pabeigtu reģistrācijas procesu</p>';
                $output.='<p>-------------------------------------------------------------</p>';
                $output.="<p>http://localhost/Auth/email_validation.php?token=$token</p>"; 
                $output.='<p>-------------------------------------------------------------</p>';
                $output.='<p>Ievadiet hipersaiti pārlūkprogrammā vai arī uzklikšķiniet uz tās.</p>';
                $output.='<p>Ja jūs nemēģinājāt reģistrētiess mūsu lapā- ignorējiet šo e-pastu.</p>';   
                $output.='<p>Ar cieņu,</p>';
                $output.='<p>Auth Chat</p>';
                $body = $output; 
                if(!smtpmailer($email,$FromEmail,$FromName,$subject,$body)) {
                    header("location: ../signup.php?error=mail");
                    exit();
                } else {
                    header("location: ../signup.php?error=none"); 
                    exit();
                }
        
        } else{
            //tu ieprieks izmantoji single sign on lai pieteiktos
            //google vai facebook
            $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ?');
            $stmt->execute(array($username));
                if ($stmt->rowCount() == 0) {
                    header("location: ../signup.php?error=emailtaken&username=$username&email=$email");
                    exit();
                } else{
                    header("location: ../signup.php?error=usernametaken&username=$username&email=$email");
                    exit();
                }
            }
    }

    public function SSOregister($username,$email,$provider)
    {        
        //lai updatotos vecais profils 
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ? or email=?');
        $stmt->execute(array($username,$email));
    //pārbaude vai lietotājs jau neeksistē
        if ($stmt->rowCount() == 0) {
            $id = uniqidReal();
            $this->db->prepare('SELECT * FROM users');
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
            $stmt = $this->db->prepare('INSERT INTO users (userID,username, email,role,verified,email_token,provider) VALUES (?,?,?,?,?,?,?)');
            $stmt->execute([$id,$username, $email, $this->role,1,NULL,$provider]);
            } else {
                //tu ieprieks izmantoji logi formu lai pieteiktos
                header("location: ../index.php?error=taken");
                exit();
            }
    }

    public function SSOlogin($email)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email=?');
        $stmt->execute(array($email));
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            session_regenerate_id();
                //padara lietotāju aktīvu
                $stmt = $this->db->prepare('UPDATE users set status = "1" where userID=?');
                $stmt->execute([$user['userID']]);
                $_SESSION['userName'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['userID'] = $user['userID'];
                $_SESSION['pwd'] = $user['password'];
                $_SESSION['role'] = $user['role'];
                user_log($this->db,$_SESSION['userID'],1);
    } 
    
}

    public function login($name_email,$pwd)
    {
        $user_id = $this->getUserID($name_email);
        if(attemptControll($this->db,$user_id)){
            header("location: ../index.php?error=toomanyattempts&name_email=$name_email");
            exit();
        }

        if(empty($name_email) || empty($pwd)){
            header("location: ../index.php?error=empty&name_email=$name_email");
            exit();
        }
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ? or email=?');
        $stmt->execute(array($name_email,$name_email));
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if($user['verified'] == 0){
                header("location: ../index.php?error=emailnotvalidated&name_email=$name_email");
                exit();
            }
            if (password_verify($pwd, $user['password'])) {
                //izmaina sesijas sīkfaila saturu
                session_regenerate_id();
                //padara lietotāju aktīvu
                $stmt = $this->db->prepare('UPDATE users set status = "1" where userID=?');
                $stmt->execute([$user['userID']]);
                $_SESSION['userName'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['userID'] = $user['userID'];
                $_SESSION['pwd'] = $user['password'];
                $_SESSION['role'] = $user['role'];
                user_log($this->db,$_SESSION['userID'],1);
                return true;
            }else{
                //timeri, lai delays pie katra neveiksmiga meginajuma
                sleep(1);
                user_log($this->db,$user_id,0);
                header("location: ../index.php?error=wrong&name_email=$name_email");
                exit();
            }
        } else{
            //timeri, lai delays pie katra neveiksmiga meginajuma
            sleep(1);
            header("location: ../index.php?error=wrong&name_email=$name_email");
            exit();
        }
    }
}
