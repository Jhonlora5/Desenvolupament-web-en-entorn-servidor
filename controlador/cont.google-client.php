<?php
require_once '../php/libs/google/autoload.php';
require_once '../model/model.socialAuth.php';

$client = new Google_Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/controlador/cont.googleAuth.php'); // Callback exclusiu per a Google
$client->addScope('email');
$client->addScope('profile');



// Si no hi ha codi de retorn, redirigim a Google per autoritzar
if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
    exit();
} else {
    // Intercanvia el codi per un token
    $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $token = $client->getAccessToken();

    // Obté informació del perfil de l'usuari
    $oauth2 = new Google_Service_Oauth2($client);
    $userProfile = $oauth2->userinfo->get();

    // Processa l'usuari
    guardaUsuariSocial(
        $userProfile->email,
        $userProfile->name,
        'Google',
        $userProfile->id,
        $token['access_token']
    );

    header('Location: ../vista/vista.formulari.php');
    exit();
}