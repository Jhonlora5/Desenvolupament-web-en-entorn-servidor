<?php
function guardaUsuariSocial($email, $displayName, $provider, $socialId, $accessToken,$pdo){
    try {
        // Busca l'usuari per email
        $stmt = $pdo->prepare('SELECT id_usuari FROM usuaris WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $usuari = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si l'usuari no existeix, l'insereix
        if (!$usuari) {
            $stmt = $pdo->prepare('INSERT INTO usuaris (nom, email, contrasenya) VALUES (:nom, :email, :contrasenya)');
            $stmt->execute([
                'nom' => $displayName,
                'email' => $email,
                'contrasenya' => password_hash('autogenerat', PASSWORD_DEFAULT), // Placeholder per contrasenya
            ]);
            $usuariId = $pdo->lastInsertId();
        } else {
            $usuariId = $usuari['id_usuari'];
        }

        // Insereix les dades socials a usuaris_socials
        $stmt = $pdo->prepare('INSERT INTO usuaris_socials (usuari_id, provider, social_id, token) 
                               VALUES (:usuari_id, :provider, :social_id, :token)');
        $stmt->execute([
            'usuari_id' => $usuariId,
            'provider' => $provider,
            'social_id' => $socialId,
            'token' => $accessToken,
        ]);
    } catch (PDOException $e) {
        echo 'Error a la base de dades: ' . $e->getMessage();
    }
}
?>