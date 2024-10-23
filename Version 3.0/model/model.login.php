<?php
/* Jonathan Lopez Ramos */
//El session_start es realitza a la funció obtenirConnexio.
require '../controlador/cont.connexio.php';
//Cridem a la funció encarregada de la connexio.
$pdo = obtenirConnexio();
//Si l'arribada de dades es de tipus POST.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Guarda la dada corresponent al id del correu.
    $email = $_POST['email'];
    //Guarda les dades corresponents al id de password
    $password = $_POST['password'];
    //Afegeix les dades del formulari per reenviar i no tornar a omplir.
    $_SESSION['dadesForm'] = $_POST;

    try {
        //Iniciem la sentencia corresponent per tal de comprovar si l'usuari existeix
        $stmt = $pdo->prepare("SELECT id_usuari, nom, contrasenya FROM usuaris WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $usuari = $stmt->fetch(PDO::FETCH_ASSOC);
        //Si la contrasenya correspon amb el que es troba a la base de dades:
        if ($usuari && password_verify($password, $usuari['contrasenya'])) {
            //Iniciar sessió
            //Afegim a $_SESSION les variables corresponents.
            $_SESSION['usuari_id'] = $usuari['id_usuari'];
            //Per controlar el temps d'inactivitat.
            $_SESSION['sessio_iniciada'] = time(); 
            $_SESSION['nom_usuari'] = $usuari['nom'];

            $_SESSION['missatgeCorrecte'] = "Sessió iniciada correctament!";
            //Retorna vista.formulari.php
            header('Location: ../vista.formulari.php');
            exit;

        } else {
            //En el cas de que la contrasenya o el correu no existeixin:
            //Retornem a l'usuari a formularilogin amb l'error.
            $_SESSION['missatgeError'] = "Correu electrònic o contrasenya incorrecta.";
            header('Location: ../vista.formularilogin.php');
            exit;
        }
    } catch (PDOException $e) {
        //En el cas de que l'error sigui per no poder establir la connexio:
        //Retornem a l'usuari a formularilogin amb l'error.
        $_SESSION['missatgeError'] = "Error al connectar a la base de dades: " . $e->getMessage();
        header('Location: ../vista.formularilogin.php');
        exit;
    }

}
?>

