<?php
require_once '../controlador/cont.connexio.php';
require __DIR__ . '/../php/libs/google/vendor/autoload.php';
require_once '../model/model.socialAuth.php';
$pdo=obtenirConnexio();


$pdo = obtenirConnexio();

// Per depuració
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Comprova si la classe de Google API existeix
    if (!class_exists('Google\Client')) {
        die('No es pot carregar la llibreria de Google API.');
    }

    // Inicialitza l'objecte del client de Google
    $client = new Google\Client();
    $client->setAuthConfig('../php/libs/google/src/credentials.json');
    $client->setRedirectUri('https://www.jlopez5.cat/controlador/cont.googleAuth.php');
    $client->addScope(['email', 'profile']);

    // Si no tenim el codi d'autenticació, redirigim a Google
    if (!isset($_GET['code'])) {
        error_log('Redirigint a Google per autenticació.');
        $authUrl = $client->createAuthUrl();
        header('Location: ' . $authUrl);
        exit();
    }

    // Si tenim un codi, el processem
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    // Verifica si el token ha expirat
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

    // Assigna les dades de l'usuari a la sessió
    $_SESSION['usuari_id'] = $userProfile->id;
    $_SESSION['nom_usuari'] = $userProfile->name;
    $_SESSION['email_usuari'] = $userProfile->email;
    $_SESSION['imatge_usuari'] = isset($userProfile->picture) ? $userProfile->picture : '../imgPerfils/default.jpg'; // Si hi ha imatge

    // Redirigeix l'usuari a la vista principal
    header('Location: ../vista/vista.formulari.php');
    exit();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>