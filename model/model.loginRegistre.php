<?php
// Funció per verificar si l'usuari ja existeix per email
function verificarUsuariPerEmail($email, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT usuaris.*, imatges_perfil.ruta AS ruta_imatge 
        FROM usuaris 
        LEFT JOIN imatges_perfil 
        ON usuaris.id_imatge = imatges_perfil.id_imatge 
        WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Error verificant l'usuari per email: " . $e->getMessage());
    }
}

// Funció per inserir un nou usuari a la base de dades
function inserirNouUsuari($nom, $email, $password, $pdo) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Encriptem la contrasenya
    $stmt = $pdo->prepare("INSERT INTO usuaris (nom, email, contrasenya) VALUES (:nom, :email, :contrasenya)");
    $stmt->execute([
        ':nom' => $nom,
        ':email' => $email,
        ':contrasenya' => $hashedPassword
    ]);
    return $pdo->lastInsertId(); // Retorna l'ID del nou usuari
}

//Funcio per revisar el token corresponent.
function revisarTokenPerIniciarSessio($token, $pdo) {
    // Comprovem si el token existeix i si és vàlid
    $stmt = $pdo->prepare("SELECT id_usuari, expiracio FROM tokens_recorda_m WHERE token = :token");
    $stmt->execute([':token' => $token]);
    $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

    //Si existeix i no ha caducat
    if ($tokenData && strtotime($tokenData['expiracio']) > time()) {
        // Iniciem sessió
        $_SESSION['usuari_id'] = $tokenData['id_usuari'];
        $_SESSION['sessio_iniciada'] = time();
        return true; // Sessió iniciada correctament
    }

    //Si no és vàlid, eliminem el token
    setcookie("recorda_token", "", time() - 3600, "/"); // Eliminem la cookie
    return false; // Retorna token invàlid o caducat
}

//Creació de la funcio per gestionar token de remerme-me
function gestionarTokenRecorda($usuari, $pdo) {
    try {
        if (isset($_POST['recorda'])) {
            $token = bin2hex(random_bytes(32));
            $expireTime = time() + (30 * 24 * 60 * 60); // 30 dies
            $stmt = $pdo->prepare("INSERT INTO tokens_recorda_m (id_usuari, token, expiracio) VALUES (:id_usuari, :token, :expiracio) 
                                    ON DUPLICATE KEY UPDATE token = :token, expiracio = :expiracio"
                                );
            $stmt->execute([':id_usuari' => $usuari['id_usuari'],':token' => $token,':expiracio' => date('Y-m-d H:i:s', $expireTime)]);
            setcookie("recorda_token", $token, $expireTime, "/", "", true, true);
        }
    } catch (PDOException $e) {
        throw new Exception("Error gestionant el token de recordatori: " . $e->getMessage());
    }
}
// Funció per manejar el registre
function manejarRegistre($nom, $email, $password, $confirmPassword, $pdo) {
    // Validacions bàsiques
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['missatgeError'] = "El format del correu electrònic no és vàlid.";
    } elseif ($password !== $confirmPassword) {
        $_SESSION['missatgeError'] = "Les contrasenyes no coincideixen.";
    } elseif (strlen($password) < 8) {
        $_SESSION['missatgeError'] = "La contrasenya ha de tenir almenys 8 caràcters.";
    } else {
        // Comprovar si l'usuari ja existeix
        if (verificarUsuariPerEmail($email, $pdo)) {
            $_SESSION['missatgeError'] = "Ja existeix un usuari amb aquest correu.";
        } else {
            // Inserir l'usuari i iniciar sessió
            $usuariId = inserirNouUsuari($nom, $email, $password, $pdo);
            $_SESSION['usuari_id'] = $usuariId;
            $_SESSION['nom_usuari'] = $nom;
            $_SESSION['missatgeCorrecte'] = "Registre completat correctament!";
            header('Location: ../vista/vista.formulari.php');
            exit;
        }
    }
    header('Location: ../vista/vista.formulariLogin.php');
    exit;
}

// Funció per manejar el login
function manejarLogin($email, $password, $pdo, $keyServer) {
    // Inicialitza els intents fallits si no existeix
    if (!isset($_SESSION['intents_fallits'])) {
        $_SESSION['intents_fallits'] = 0;
    }

    $usuari = verificarUsuariPerEmail($email, $pdo);

    // Comprovem si l'usuari existeix i si la contrasenya és correcta
    if (!$usuari || !password_verify($password, $usuari['contrasenya'])) {
        // Incrementa els intents fallits
        $_SESSION['intents_fallits']++;

        // Mostra un missatge d'error
        $_SESSION['missatgeError'] = "Correu electrònic o contrasenya incorrecta.";

        // Redirigeix a la pàgina de login
        header('Location: ../vista/vista.formulariLogin.php');
        exit;
    }

    // Comprovació de intents fallits i reCAPTCHA
    if ($_SESSION['intents_fallits'] >= 3) {
        if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
            $_SESSION['missatgeError'] = "Per continuar, completa el reCAPTCHA.";
            header('Location: ../vista/vista.formulariLogin.php');
            exit;
        }

        $recaptchaResponse = $_POST['g-recaptcha-response'];
        $verificaResposta = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$keyServer&response=$recaptchaResponse");
        $verificaDades = json_decode($verificaResposta);

        if (!$verificaDades->success) {
            $_SESSION['missatgeError'] = "Verificació reCAPTCHA fallida.";
            header('Location: ../vista/vista.formulariLogin.php');
            exit;
        }
    }

    // Login correcte
    $_SESSION['usuari_id'] = $usuari['id_usuari'];
    $_SESSION['nom_usuari'] = $usuari['nom'];
    $_SESSION['nivell_administrador'] = $usuari['nivell_administrador'];
    $_SESSION['imatge_perfil'] = $usuari['ruta_imatge'];

    // Restableix els intents fallits a 0
    $_SESSION['intents_fallits'] = 0;

    // Gestiona el token "Recorda'm" si cal
    gestionarTokenRecorda($usuari, $pdo);

    // Mostra un missatge d'èxit
    $_SESSION['missatgeCorrecte'] = "Sessió iniciada correctament!";
    header('Location: ../vista/vista.formulari.php');
    exit;
}
?>