<?php
require_once './vendor/autoload.php';

if (!session_id()) {
    session_start();
}

// Call Facebook API

$fb = new Facebook\Facebook([
    'app_id'      => '1126270074475386',
    'app_secret'     => '0e84af2b7f631ee87af87b61702fe10a',
    'default_graph_version'  => 'v2.10'
]);
$permissions = ['email'];
$helper = $fb->getRedirectLoginHelper();
$login_url = $helper->getLoginUrl("http://localhost/Diplomdarbs/", $permissions);

try {
    $accessToken = $helper->getAccessToken();
    if (isset($accessToken)) {
        $_SESSION['access_token'] = (string)$accessToken;
        header("Location:index.php");
    }
} catch (\Throwable $th) {
    //throw $th;
}

if (isset($_SESSION['access_token'])) {
    try {
        $fb->setDefaultAccessToken($_SESSION['access_token']);
        $profile_request = $fb->get('/me?fields=name,first_name,last_name,email');
        $user_info = $profile_request->getGraphUser();
        $fbid = $user_info->getProperty('id');
        $fbfullname = $user_info->getProperty('name');   // To Get Facebook full name
        $fbemail = $user_info->getProperty('email');    //  To Get Facebook email
        $_SESSION['userName'] = $fbfullname;
        $_SESSION['email'] = $fbemail;
        $_SESSION['id'] = $fbid;
        header("Location:chat.php");
    } catch (Exception $exc) {
        echo $exc->getTraceAsString();
        session_destroy();
        header("Location: ../index.php");
        exit;
    }
}
