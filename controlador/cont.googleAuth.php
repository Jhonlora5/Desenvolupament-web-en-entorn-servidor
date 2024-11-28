<?php
require_once '../controlador/cont.connexio.php';
require_once __DIR__ . '/ ../php/libs/google/vendor/autoload.php';
require_once '../model/model.socialAuth.php';

$pdo=obtenirConnexio();


try {
    $client = new Google\Client();
    $client->setAuthConfig('../php/libs/google/credentials.json');
    $client->setRedirectUri('https://www.jlopez5.cat/controlador/cont.googleAuth.php');
    $client->addScope(['email', 'profile']);

    if (!isset($_GET['code'])) {
        // Si no tenim un codi, redirigim a l'usuari per autoritzar
        $authUrl = $client->createAuthUrl();
        header('Location: ' . $authUrl);
        exit();
    } else {
        // Obtenim el token d'accés
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token);

        if ($client->isAccessTokenExpired()) {
            throw new Exception('Token d\'accés expirat.');
        }

        // Obtenim la informació de l'usuari
        $oauth2 = new Google\Service\Oauth2($client);
        $userProfile = $oauth2->userinfo->get();

        // Processa l'usuari
        processarUsuariGoogle(
            $userProfile['email'],
            $userProfile['name'],
            $userProfile['id'],
            $token['access_token'],
            $userProfile['picture'], // URL de la imatge
            $pdo
        );
        $_SESSION['usuari_id'] = $userProfile->id;
        $_SESSION['nom_usuari'] = $userProfile->name;
        $_SESSION['email_usuari'] = $userProfile->email;
        $_SESSION['imatge_usuari'] = isset($userProfile->picture) ? $userProfile->picture : '../imgPerfils/default.jpg'; // Si hi ha imatge
        // Redirigeix l'usuari a la vista principal
        header('Location: ../vista/vista.formulari.php');
        exit();
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>