<?php
require_once 'C:\laragon\www\Auth\vendor\autoload.php';
require_once 'databasecontroll.php';

date_default_timezone_set('Europe/Riga');

// Database configuration
define('DB_HOST', '127.0.0.1.');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'chat');

// Google API configuration
define('GOOGLE_CLIENT_ID', '821311186890-seba31c0kvf3d9dpndi5tgfl2jbc8mrk');
define('GOOGLE_CLIENT_SECRET', 'oMDwwXP0_IzdLsDI5XA_MYje');
define('GOOGLE_REDIRECT_URL', 'http://localhost/Auth/index.php');

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


