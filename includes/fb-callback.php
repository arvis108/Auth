<?php
//https://github.com/facebookarchive/php-graph-sdk
//initialize facebook sdk
require '../vendor/autoload.php';
require 'userClass.php';
require 'databasecontroll.php';
if (!session_id()) {
    session_start();
}
$fb = new Facebook\Facebook([
    'app_id'      => '1126270074475386',
    'app_secret'     => '0e84af2b7f631ee87af87b61702fe10a',
    'default_graph_version'  => 'v2.10'
]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; //pieejas tiesības E-pastam

try {
if (isset($_SESSION['facebook_access_token'])) {
$accessToken = $_SESSION['facebook_access_token'];
} else {
  $accessToken = $helper->getAccessToken();
}
} catch(Facebook\Exceptions\facebookResponseException $e) {
// When Graph returns an error
echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
// When validation fails or other local issues
echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}
if (isset($accessToken)) {
if (isset($_SESSION['facebook_access_token'])) {
$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
} else {
// getting short-lived access token
$_SESSION['facebook_access_token'] = (string) $accessToken;
  // OAuth 2.0 client handler
$oAuth2Client = $fb->getOAuth2Client();
// Exchanges a short-lived access token for a long-lived one
$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
// setting default access token to be used in script
$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
}

// getting basic info about user
try {
$profile_request = $fb->get('/me?fields=name,first_name,last_name,email');
$profile = $profile_request->getGraphUser();
$fbusername = $profile->getField('name');   // To Get Facebook full name
$fbemail = $profile->getField('email');    //  To Get Facebook email


$user = new Users($conn);
$stmt = $conn->prepare('SELECT * FROM users WHERE email=?');
        $stmt->execute(array($fbemail));
        if ($stmt->rowCount() > 0) {
        $stmt = $conn->prepare('UPDATE users SET provider = ? WHERE email = ?');
        $stmt->execute(['facebook',$fbemail]);
        } else {
          $user->SSOregister($fbusername,$fbemail,'facebook');
        }
$user->SSOlogin($fbemail);
// redirect the user to the profile page if it has "code" GET variable
if (isset($_GET['code'])) {
  header('Location: ../rooms.php');
  }
} catch(Facebook\Exceptions\FacebookResponseException $e) {
// When Graph returns an error
echo 'Graph returned an error: ' . $e->getMessage();
session_destroy();
// redirecting user back to app login page
header("Location: ../index.php");
exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
// When validation fails or other local issues
echo 'Facebook SDK returned an error: ' . $e->getMessage();
exit;
}
} else {
// replace your website URL same as added in the developers.Facebook.com/apps e.g. if you used http instead of https and you used            
$login_url = $helper->getLoginUrl("http://localhost/Auth/index.php", $permissions);

}
?>



