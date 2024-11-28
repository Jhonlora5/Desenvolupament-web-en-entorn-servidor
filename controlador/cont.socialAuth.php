<?php
require_once '../controlador/cont.connexio.php';
require_once '../php/libs/hybridauth/src/autoload.php'; // Assegura't que la ruta sigui correcta
require_once '../model/model.socialAuth.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$hybridauthConfig = [
    'callback' => 'http://localhost/controlador/cont.socialAuth.php?provider=GitHub',
    'providers' => [
        'GitHub' => [
            'enabled' => true,
            'keys' => [
                'id' => 'Ov23liFIcB7Suam6cxBd', // Substitueix amb el teu Client ID de GitHub
                'secret' => '6059d23f887544ebb0761826bda036679dcd20ea', // Substitueix amb el teu Client Secret de GitHub
            ],
            #'scope' => 'user:email', // Permisos per accedir al perfil i al correu
        ],
    ],
];

use Hybridauth\Hybridauth;
use Hybridauth\Exception\Exception;

error_log('Inici del controlador: ' . print_r($_GET, true));

$provider = $_GET['provider'] ?? null;

var_dump($GET);
var_dump($provider);

if ($provider) {
    try {
        $hybridauth = new Hybridauth($hybridauthConfig);
        $adapter = $hybridauth->authenticate($provider);
        $userProfile = $adapter->getUserProfile();
        $pdo = obtenirConnexio();
        //var_dump($pdo);
        // Guarda l'usuari a la base de dades
        guardaUsuariSocial(
            $userProfile->email,
            $userProfile->displayName,
            $provider,
            $userProfile->identifier,
            $adapter->getAccessToken(),
            $pdo 
        );

        // Desa informació de l'usuari a la sessió
        $_SESSION['user'] = [
            'identifier' => $userProfile->identifier,
            'email' => $userProfile->email,
            'firstName' => $userProfile->firstName,
            'lastName' => $userProfile->lastName,
            'provider' => $provider,
        ];

        // Redirigeix a la vista principal
        header('Location: ../vista/vista.formulari.php');
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>