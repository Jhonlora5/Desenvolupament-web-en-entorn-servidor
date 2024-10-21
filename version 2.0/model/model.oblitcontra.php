<?php
/* Jonathan Lopez Ramos */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Inclou PHPMailer
require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

require_once __DIR__ . '/../controlador/cont.connexio.php';

$pdo = obtenirConnexio();

/*// Comprovar si la funció existeix
if (function_exists('obtenirConnexio')) {
    // Cridar la funció
    $pdo = obtenirConnexio();
} else {
    echo "La funció obtenirConnexio no està definida.";
}*/


// Assegurar que PDO està disponible
if (!$pdo) {
    die("Error: No es pot establir la connexió amb la base de dades.");
    print_r("parece que llega vacia");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Verificar si el correu electrònic existeix
    $stmt = $pdo->prepare("SELECT id_usuari, nom FROM usuaris WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuari = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuari) {
        // Generar un token de recuperació
        $token = bin2hex(random_bytes(50));
        $expiracio = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Comprovar si ja existeix una entrada per a aquest correu electrònic a la taula recuperacio_contrasenya
        $stmt = $pdo->prepare("SELECT * FROM recuperacio_contrasenya WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $existeix = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existeix) {
            // Actualitzar el token i l'expiració si ja existeix una entrada
            $stmt = $pdo->prepare("UPDATE recuperacio_contrasenya SET token = :token, expiracio = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
        } else {
            // Si no existeix, inserir una nova entrada
            $stmt = $pdo->prepare("INSERT INTO recuperacio_contrasenya (email, token, expiracio) VALUES (:email, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
        }

        //Configuracio PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            //Servei SMTP
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            //El teu correu
            $mail->Username = 'j.lopez5@sapalomera.cat';
            //La teva contrasenya
            $mail->Password = 'yndf uqmg dhxv crii';
            //O PHPMailer::ENCRYPTION_SMTPS
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            //Port SMTP 
            $mail->Port = 587; 

            //Destinatari
            $mail->setFrom('j.lopez5@sapalomera.cat', 'Jonathan');
            $mail->addAddress($email);

            //Contingut del correu
            $mail->isHTML(true);
            $mail->Subject = 'Recuperació de Contrasenya';
            $mail->Body = "Fes clic <a href='http://localhost/vista.reestableix_contrasenya.php?token=$token'>aquí</a> per restablir la teva contrasenya. El token expira en 1 hora.";
            //Enviament del correu
            $mail->send();
            
            //Redirigeix a la pàgina de formularilogin
            header('Location: ../vista.formularilogin.php'); 
            exit;
            //Enviament del missatge per tal de mostrar que s'ha realitzar correctament.
            $_SESSION['missatge'] = 'El correu de recuperació de '. $username .'\'ha enviat amb èxit. Comprova la teva safata d\'entrada.';
        } catch (Exception $e) {
            //Enviament del missatge per tal de mostrar que existeix un error.
            $_SESSION['missatge'] = 'No s\'ha pogut enviar el correu. Error: ' . $mail->ErrorInfo;
        }
    } else {

        $_SESSION['missatge'] = 'No hi ha cap compte associat a aquest correu electrònic.';
    }
    $_SESSION['missatge'] = 'No s\'a tobat usuari.';
    header('Location: ../vista.oblit.php'); // Redirigeix oblit.php
    exit();
}
?>
