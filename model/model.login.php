<?php
/* Jonathan Lopez Ramos */
//El session_start es realitza a la funció obtenirConnexio.
require '../controlador/cont.processar.php';
//Cridem a la funció encarregada de la connexio.
$pdo = obtenirConnexio();
//Afegim la key del server per a recaptcha en una variable
$keyServer = $_SESSION['key-server'];


//Funcio per revisar el token corresponent.
function revisarTokenPerIniciarSessio($token, $pdo) {
    // Comprovem si el token existeix i si és vàlid
    $stmt = $pdo->prepare("SELECT id_usuari, expiracio FROM tokens_recorda_m WHERE token = :token");
    $stmt->execute([':token' => $token]);
    $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si existeix i no ha caducat
    if ($tokenData && strtotime($tokenData['expiracio']) > time()) {
        // Iniciem sessió
        $_SESSION['usuari_id'] = $tokenData['id_usuari'];
        $_SESSION['sessio_iniciada'] = time();
        return true; // Sessió iniciada correctament
    }

    // Si no és vàlid, eliminem el token
    setcookie("recorda_token", "", time() - 3600, "/"); // Eliminem la cookie
    return false; // Token invàlid o caducat
}

//Creació de la funcio per gestionar token
function gestionarTokenRecorda($usuari, $pdo) {
    if (isset($_POST['recorda'])) {
        // Revisem si existeix un token i si ha caducat
        $stmtToken = $pdo->prepare("SELECT token, expiracio FROM tokens_recorda_m WHERE id_usuari = :id_usuari");
        $stmtToken->execute([':id_usuari' => $usuari['id_usuari']]);
        $tokenData = $stmtToken->fetch(PDO::FETCH_ASSOC);

        // Si el token ha caducat, generem un nou token
        if ($tokenData && strtotime($tokenData['expiracio']) <= time()) {
            $token = bin2hex(random_bytes(32)); // Generar token segur
            $expireTime = time() + (30 * 24 * 60 * 60); // 30 dies
            $stmtToken = $pdo->prepare("INSERT INTO tokens_recorda_m (id_usuari, token, expiracio) VALUES (:id_usuari, :token, :expiracio) ON DUPLICATE KEY UPDATE token = :token, expiracio = :expiracio");
            $stmtToken->execute([
                ':id_usuari' => $usuari['id_usuari'],
                ':token' => $token,
                ':expiracio' => date('Y-m-d H:i:s', $expireTime)
            ]);
            setcookie("recorda_token", $token, $expireTime, "/", "", true, true); // Establir la cookie
        } else {
            // Si el token no ha caducat, utilitzem el token existent
            setcookie("recorda_token", $tokenData['token'], strtotime($tokenData['expiracio']), "/", "", true, true);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Inicialitzar intent de fallida si no existeix
    if (!isset($_SESSION['intents_fallits'])) {
        $_SESSION['intents_fallits'] = 0;
    }

    // Recollir les dades del formulari
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Funció per verificar l'usuari per email
    $usuari = verificarUsuariPerEmail($email, $pdo);

    // Si l'usuari no existeix o la contrasenya és incorrecta
    if (!$usuari || !password_verify($password, $usuari['contrasenya'])) {
        $_SESSION['intents_fallits']++;
        $_SESSION['missatgeError'] = "Correu electrònic o contrasenya incorrecta.";
        header('Location: ../vista/vista.formulariLogin.php');
        exit;
    }

    // Comprovació de reCAPTCHA si hi ha més de 3 intents fallits
    if ($_SESSION['intents_fallits'] >= 3) {
        $recaptchaResponse = $_POST['g-recaptcha-response'];
        $verificaResposta = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$keyServer&response=$recaptchaResponse");
        $verificaDades = json_decode($verificaResposta);

        if (!$verificaDades->success) {
            $_SESSION['missatgeError'] = "Verificació reCAPTCHA fallida. Torna-ho a intentar.";
            header('Location: ../vista/vista.formulariLogin.php');
            exit;
        }
    }

    // Restablir intents fallits després d'un login correcte
    $_SESSION['intents_fallits'] = 0;

    // Si el checkbox 'recorda' està marcat, gestionem el token
    gestionarTokenRecorda($usuari, $pdo);

    // Inicialitzar la sessió
    $_SESSION['usuari_id'] = $usuari['id_usuari'];
    $_SESSION['sessio_iniciada'] = time();
    $_SESSION['nom_usuari'] = $usuari['nom'];
    $_SESSION['missatgeCorrecte'] = "Sessió iniciada correctament!";

    // Redirigir a la vista de formulari
    header('Location: ../vista/vista.formulari.php');
    exit;
}
?>