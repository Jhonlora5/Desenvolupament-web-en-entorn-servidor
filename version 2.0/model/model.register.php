<?php
/* Jonathan Lopez Ramos */
//Crida a la connexió per poguer fer la crida a la funció i guardarla en la variable corresponent.
require_once '../controlador/cont.connexio.php';
//Creació de la crida a la funció de la connexió.
$pdo = obtenirConnexio();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    //Utilittzem el filtre de validació de correus per tal de mirar si es o no un.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //Afegim l'error corresponent a la session missatgeerror.
        $_SESSION['missatgeError'] = "El format del correu electrònic no és vàlid.";
    } elseif ($password !== $confirmPassword) {
        //Si la entrada dels passwords no correspon un amb l'altre.
        $_SESSION['missatgeError'] = "Les contrasenyes no coincideixen.";
    } elseif (strlen($password) < 8) {
        //Si es inferior a la mida de 8 caracters.
        $_SESSION['missatgeError'] = "La contrasenya ha de tenir almenys 8 caràcters.";
    } else {        
        try {
            //Comprovar si l'usuari ja existeix
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuaris WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                //(Rectificacio d'errors)
                $_SESSION['missatgeError'] = "Ja existeix un usuari amb aquest correu.";
            } else {
                //Inserir l'usuari nou
                //Encriptacio del password per la posterior insercio de les dades.
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                //statment corresponent a la crida de la insercio on:
                $stmt = $pdo->prepare("INSERT INTO usuaris (nom, email, contrasenya) VALUES (:nom, :email, :contrasenya)");
                //Establim que el seu nom es la idnom del formulari.
                $stmt->bindParam(':nom', $nom);
                //Establim que el seu correu es la idcorreu del formulari.
                $stmt->bindParam(':email', $email);
                //Establim que el seu contrasenya es la idcontrasenya del formulari amb la encriptacio corresponent.
                $stmt->bindParam(':contrasenya', $hashedPassword);
                //Executem la instruccio.
                $stmt->execute();
                //Recuperem el nom per tal de mostrar-lo a la finestre corresponent de formulari.php
                $_SESSION['nom_usuari'] = $nom;

                $_SESSION['missatgeCorrecte'] = "Registre completat correctament!";
                //En cas de que tot sigui correcta es reenvia a formulari.php.
                header('Location: ../vista.formulari.php');
                exit;
            }
        } catch (PDOException $e) {
                //Si existeix error reenviem a formularilogin
                $_SESSION['missatgeError'] = "Error al registrar l'usuari en la base de dades: " . $e->getMessage();
                header('Location: ../vista.formularilogin.php');
                exit;
        }
    }
    //Si existeixen errors reenviem al formularilogin.
    $_SESSION['missatgeError'] = "Error o errors en la validació de correus: " . $e->getMessage();
    header('Location: ../vista.formularilogin.php');
    exit;
}
?>

