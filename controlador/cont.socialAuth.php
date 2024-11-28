<?php
require_once '../controlador/cont.connexio.php';
require_once '../php/libs/hybridauth/src/autoload.php';
require_once '../model/model.socialAuth.php';


$hybridauthConfig = [
    'callback' => 'http://localhost/controlador/cont.socialAuth.php?provider=GitHub',
    'providers' => [
        'GitHub' => [
            'enabled' => true,
            'keys' => [
                'id' => 'Ov23liFIcB7Suam6cxBd', 
                'secret' => '6059d23f887544ebb0761826bda036679dcd20ea', 
            ],
        ],
    ],
];

use Hybridauth\Hybridauth;
use Hybridauth\Exception\Exception;

$provider = $_GET['provider'] ?? null;

if (!$provider) {
    echo "Cap proveïdor especificat.";
    exit();
}

try {
    // Inicialitza Hybridauth i autentica l'usuari
    $hybridauth = new Hybridauth($hybridauthConfig);
    $adapter = $hybridauth->authenticate($provider);
    $userProfile = $adapter->getUserProfile();
    $pdo = obtenirConnexio();

    // Busca l'usuari social a la base de dades
    $usuariSocial = verificarUsuariPerIdentifier($userProfile->identifier, $provider, $pdo);

    if ($usuariSocial) {
        // Si l'usuari ja existeix, carreguem les dades a la sessió
        $_SESSION['usuari_id'] = $usuariSocial['id_usuari'];
        $_SESSION['nom_usuari'] = $usuariSocial['nom'];
        $_SESSION['imatge_perfil'] = $usuariSocial['imatge_perfil'] ?? 'imgPerfils/default.jpg'; // Ruta per defecte si no té imatge
        $_SESSION['missatgeCorrecte'] = "Sessió iniciada correctament amb GitHub!";
        header('Location: ../vista/vista.formulari.php');
        exit();

    } else {
        // Si no existeix, el guardem a la base de dades
        guardaUsuariSocial(
            $userProfile->email,
            $userProfile->displayName,
            $provider,
            $userProfile->identifier,
            $adapter->getAccessToken(),
            $pdo
        );

        // Torna a buscar l'usuari social per carregar-lo a la sessió
        $usuariSocial = verificarUsuariPerIdentifier($userProfile->identifier, $provider, $pdo);

        if ($usuariSocial) {
            $_SESSION['usuari_id'] = $usuariSocial['id_usuari'];
            $_SESSION['nom_usuari'] = $usuariSocial['nom'];
            $_SESSION['imatge_perfil'] = $usuariSocial['imatge_perfil'] ?? 'imgPerfils/default.jpg'; 
            $_SESSION['missatgeCorrecte'] = "Sessió iniciada i usuari registrat amb GitHub!";
        }
    }

    // Redirigeix a la vista principal
    header('Location: ../vista/vista.formulari.php');
    exit();
} catch (Exception $e) {
    // Gestiona errors durant el procés d'autenticació
    echo 'Error: ' . $e->getMessage();
}
?>