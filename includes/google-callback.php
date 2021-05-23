<?php 
//https://www.webslesson.info/2019/09/how-to-make-login-with-google-account-using-php.html

require_once 'config.php';

if(isset($_GET["code"])) {
        $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);
        if(!isset($token['error'])) {
            $google_client->setAccessToken($token['access_token']);
            $_SESSION['access_token'] = $token['access_token'];
            $google_service = new Google_Service_Oauth2($google_client);
            $data = $google_service->userinfo->get();

            $name = htmlspecialchars($data['given_name']);
            $surname = htmlspecialchars($data['family_name']);
            $email = $data['email'];
            
            $username = $name.$surname;
            $user = new Users($conn);
            $stmt = $conn->prepare('SELECT * FROM users WHERE email=?');
            $stmt->execute(array($email));
                if ($stmt->rowCount() > 0) {
                    $stmt = $conn->prepare('UPDATE users SET provider = ? WHERE email = ?');
                    $stmt->execute(['google',$email]);
                } else {
                    $user->SSOregister($username,$email,'google');
                }
            $user->SSOlogin($email);
            header("location: ../rooms.php");
        }
}

if(!isset($_SESSION['access_token']))
{
    $login_url = $google_client->createAuthUrl();
}


