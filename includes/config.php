<?php
require_once 'C:\laragon\www\Auth\vendor\autoload.php';
require_once 'userClass.php';

date_default_timezone_set('Europe/Riga');
// Google API configuration
define('GOOGLE_CLIENT_ID', '821311186890-seba31c0kvf3d9dpndi5tgfl2jbc8mrk');
define('GOOGLE_CLIENT_SECRET', 'oMDwwXP0_IzdLsDI5XA_MYje');
define('GOOGLE_REDIRECT_URL', 'http://localhost/Auth/index.php');

//Facebook API configuration
define('FACEBOOK_CLIENT_ID', '1126270074475386');
define('FACEBOOK_CLIENT_SECRET', '0e84af2b7f631ee87af87b61702fe10a');


//Passqord pepper
define('PEPPER', 'c1isvFdxM42dawfm0OlvxpecFw');

// Start session
if(!session_id()){
    session_start();
}

// Call Google API
$google_client = new Google_Client();
$google_client->setClientId(GOOGLE_CLIENT_ID);
$google_client->setClientSecret(GOOGLE_CLIENT_SECRET);
$google_client->setRedirectUri(GOOGLE_REDIRECT_URL);
$google_client->addScope('email');
$google_client->addScope('profile');


