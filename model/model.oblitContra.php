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
        //Generar un token de recuperació
        $token = bin2hex(random_bytes(50));
        $expiracio = date('Y-m-d H:i:s', strtotime('+1 hour'));

        //Comprovar si ja existeix una entrada per a aquest correu electrònic a la taula recuperacio_contrasenya
        $stmt = $pdo->prepare("SELECT * FROM recuperacio_contrasenya WHERE id_usuari = :id_usuari");
        $stmt->bindParam(':id_usuari', $usuari['id_usuari']);
        $stmt->execute();
        $existeix = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existeix) {
            //Actualitzar el token i l'expiració si ja existeix una entrada
            $stmt = $pdo->prepare("UPDATE recuperacio_contrasenya SET token = :token, expiracio = :expiracio, email = :email WHERE id_usuari = :id_usuari");
            $stmt->bindParam(':id_usuari', $usuari['id_usuari']);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expiracio', $expiracio);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
        } else {
            //Si no existeix, inserir una nova entrada
            $stmt = $pdo->prepare("INSERT INTO recuperacio_contrasenya (id_usuari, token, expiracio, email) VALUES (:id_usuari, :token, :expiracio, :email)");
            $stmt->bindParam(':id_usuari', $usuari['id_usuari']);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expiracio', $expiracio);
            $stmt->bindParam(':email', $email);
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
            $mail->Body = "Fes clic <a href='http://localhost/vista/vista.reestableix_contrasenya.php?token=$token'>Clica aquí</a> per restablir la teva contrasenya. El token expira en 1 hora.";
            //Enviament del correu
            $mail->send();            
            
            //Enviament del missatge per tal de mostrar que s'ha realitzar correctament.
            $_SESSION['missatgeCorrecte'] = 'El correu de recuperació de '. $usuari['nom'] .' s\'ha enviat amb èxit. Comprova la teva safata d\'entrada.';
            //Redirigeix a la pàgina de formularilogin
            header('Location: ../vista/vista.formulariLogin.php');
            exit;
            
        } catch (Exception $e) {
            //Enviament del missatge per tal de mostrar que existeix un error.
            $_SESSION['missatgeError'] = 'No s\'ha pogut enviar el correu. Error: ' . $mail->ErrorInfo;
        }
    } else {

        $_SESSION['missatgeError'] = 'No hi ha cap compte associat a aquest correu electrònic.';
    }
    $_SESSION['missatgeError'] = 'No hi ha cap compte associat a aquest correu electrònic.';
    header('Location: ../vista/vista.oblit.php'); // Redirigeix oblit.php
    exit();
}
//https://www.jlopez5.cat/vista.reestableix_contrasenya.php?token=$token
?>
