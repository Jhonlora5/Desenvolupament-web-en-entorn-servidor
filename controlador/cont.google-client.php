<?php
require_once '../php/libs/google/autoload.php';

$client = new Google_Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/controlador/cont.socialAuth.php');
$client->addScope('email');
$client->addScope('profile');
?>