<?php
function guardaUsuariSocial($email, $displayName, $provider, $socialId, $accessToken, $pdo) {
    try {
        // Busca si ja existeix un usuari social amb el mateix social_id i provider
        $stmt = $pdo->prepare('SELECT usuari_id FROM usuaris_socials WHERE social_id = :social_id AND provider = :provider');
        $stmt->execute([
            'social_id' => $socialId,
            'provider' => $provider,
        ]);
        $usuariSocial = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuariSocial) {
            // Si ja existeix, només actualitzem el token
            $stmt = $pdo->prepare('UPDATE usuaris_socials SET token = :token WHERE social_id = :social_id AND provider = :provider');
            $stmt->execute([
                'token' => $accessToken,
                'social_id' => $socialId,
                'provider' => $provider,
            ]);
            return; // Ja existeix, sortim
        }

        // Si no existeix, creem un nou usuari i registrem les dades socials
        // Primer, mirem si hi ha un usuari amb el mateix email (opcional)
        $usuariId = null;
        if (!empty($email)) {
            $stmt = $pdo->prepare('SELECT id_usuari FROM usuaris WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $usuari = $stmt->fetch(PDO::FETCH_ASSOC);
            $usuariId = $usuari['id_usuari'] ?? null;
        }

        // Si no hi ha usuari amb aquest email, creem un nou registre a la taula usuaris
        if (!$usuariId) {
            $stmt = $pdo->prepare('INSERT INTO usuaris (nom, email, contrasenya) VALUES (:nom, :email, :contrasenya)');
            $stmt->execute([
                'nom' => $displayName,
                'email' => $email ?? null, // Pot ser NULL si no tenim email
                'contrasenya' => password_hash('autogenerat', PASSWORD_DEFAULT), // Placeholder per contrasenya
            ]);
            $usuariId = $pdo->lastInsertId();
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
function verificarUsuariPerIdentifier($identifier, $provider, $pdo) {
    try {
        $stmt = $pdo->prepare('SELECT 
        u.id_usuari, 
        u.nom, 
        i.ruta AS imatge_perfil
        FROM usuaris_socials us
        JOIN usuaris u ON us.usuari_id = u.id_usuari
        LEFT JOIN imatges_perfil i ON u.id_imatge = i.id_imatge
        WHERE us.social_id = :identifier AND us.provider = :provider');
        
        $stmt->execute([':identifier' => $identifier, ':provider' => $provider]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna l'usuari i la seva imatge de perfil si existeix
    } catch (PDOException $e) {
        throw new Exception("Error verificant l'usuari per identifier: " . $e->getMessage());
    }
}
?>