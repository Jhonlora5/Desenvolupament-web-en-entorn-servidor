-- Jonathan López Ramos
-- Eliminacio de la base de dades si existeix

/*DROP DATABASE IF EXISTS pt05_jonathan_lopez;
DROP TABLE IF EXISTS articles;*/

/*DROP TABLE usuaris;
DROP TABLE recuperacio_contrasenya;*/
-- Creació de la base de dades

CREATE DATABASE IF NOT EXISTS pt05_jonathan_lopez CHARACTER SET utf8mb3 COLLATE utf8mb3_bin;

USE pt05_jonathan_lopez;

CREATE TABLE articles (
    id_article INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    cos TEXT NOT NULL,
    quantitat_disponible INT,
    preu INT,
    img_path VARCHAR(255) -- Ruta para la imagen
);

CREATE TABLE usuaris (
    id_usuari INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    contrasenya VARCHAR(255) NOT NULL,
    data_registre TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    nivell_administrador INT DEFAULT 2,
    id_imatge INT DEFAULT 2,
    CONSTRAINT fk_usuari_imatge FOREIGN KEY (id_imatge) REFERENCES imatges_perfil(id_imatge)
);

CREATE TABLE recuperacio_contrasenya (
    id_rec_contra INT AUTO_INCREMENT PRIMARY KEY,
    id_usuari INT NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    token VARCHAR(100) NOT NULL,
    expiracio DATETIME NOT NULL,
    FOREIGN KEY (id_usuari) REFERENCES usuaris(id_usuari) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tokens_recorda_m (
    id_token INT AUTO_INCREMENT PRIMARY KEY,
    id_usuari INT NOT NULL,
    token VARCHAR(100) NOT NULL,
    expiracio DATETIME NOT NULL,
    FOREIGN KEY (id_usuari) REFERENCES usuaris(id_usuari) ON DELETE CASCADE
);

CREATE TABLE compres (
    id_compra INT AUTO_INCREMENT PRIMARY KEY,   
    quantitat INT,
    preu_total INT,
    data_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    fk_article_articles INT,
    fk_usuari_usuaris INT,
    FOREIGN KEY (fk_article_articles) REFERENCES articles(id_article) ON DELETE CASCADE,
    FOREIGN KEY (fk_usuari_usuaris) REFERENCES usuaris(id_usuari) ON DELETE CASCADE
);
-- Crear la taula d'imatges de perfil
CREATE TABLE imatges_perfil (
    id_imatge INT AUTO_INCREMENT PRIMARY KEY,
    ruta VARCHAR(255) NOT NULL UNIQUE DEFAULT '../imgPerfils/default.jpg'
);

-- Creacio de la taula d'usuaris socials
CREATE TABLE usuaris_socials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuari_id INT NOT NULL,
    provider VARCHAR(255) NOT NULL,
    social_id VARCHAR(255) NOT NULL,
    token VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuari_id) REFERENCES usuaris(id_usuari)
);

-- Inserir imatges predefinides a la taula
INSERT INTO imatges_perfil (ruta) 
VALUES 
('imgPerfils/default.jpg'),
('imgPerfils/cientifico.png'),
('imgPerfils/joven.jpeg'),
('imgPerfils/jovenHombre.jpg'),
('imgPerfils/senyor.png'),
('imgPerfils/senyorCanas.png');



-- Inserció de dades a la taula articles.

INSERT INTO articles (nom, cos, img_path, quantitat_disponible, preu) VALUES
('Poma', "Denominació d'origen de la Poma de Girona", '../img/poma.jpg', 100, 2),
('Platan', "Platan de les illes Canaries, denominació d'origen", '../img/platan.jpg', 200, 2),
('Cirera', "La Denominació d'Origen de la cirera del Jerte", '../img/cirera.jpg', 150, 4),
('Presec', "La denominació d'origen del prèsec de Calanda", '../img/presec.jpg', 120, 3),
('Pera', 'Rectifica aquest text', '../img/pera.jpg', 180, 2),
('Mango', 'El mango és una fruita tropical dolça amb una polpa suculenta de color groc taronja.', '../img/mango.jpg', 110, 5),
('Kiwi', 'El kiwi és conegut per la seva pell peluda i la seva carn verda amb un sabor àcid i dolç.', '../img/kiwi.jpg', 130, 3),
('Maduixa', 'La maduixa és una fruita petita i vermella, dolça i suculenta, ideal per a postres.', '../img/maduixa.jpg', 90, 3),
('Raïm', 'Els raïms són petites baies dolces, típiques en racims, amb varietats verdes i negres.', '../img/raim.jpg', 170, 4),
('Taronja', 'La taronja és una fruita cítrica rica en vitamina C, amb una pell taronja i polpa sucosa.', '../img/taronja.jpg', 140, 2),
('Llimona', 'La llimona és una fruita cítrica àcida, sovint utilitzada per a sucs i amanides.', '../img/Llimona.jpg', 160, 2),
('Alvocat', "L'alvocat és una fruita cremosa amb un alt contingut en greixos saludables, ideal per fer guacamole.", '../img/alvocat.jpg', 100, 4),
('Tomàquet', 'El tomàquet és una fruita vermella que es fa servir com a verdura en moltes receptes mediterrànies.', '../img/tomaquet.jpg', 190, 2),
('Pebrot', 'El pebrot pot ser dolç o picant i es presenta en diferents colors com verd, vermell i groc.', '../img/pebrot.jpg', 130, 3),
('Ceba', "La ceba és una verdura essencial en la cuina, amb un sabor fort i aromàtic quan es cuina.", '../img/ceba.jpg', 220, 1),
('Cogombre', 'El cogombre és una hortalissa fresca i cruixent, sovint utilitzada en amanides.', '../img/cogombre.jpg', 200, 2),
('Carabassa', 'La carabassa és una verdura gran i taronja, popular en purés i postres.', '../img/carabassa.jpg', 90, 4),
('Moniato', 'El moniato és un tubercle dolç i ric en nutrients, perfecte per a plats dolços o salats.', '../img/moniato.jpg', 80, 3),
('Pinya', 'La pinya és una fruita tropical dolça i àcida amb una pell gruixuda i espinosa.', '../img/pinya.jpg', 70, 5),
('Cíndria', 'La síndria és una fruita refrescant i gran amb una polpa vermella i dolça plena d’aigua.', '../img/cindria.jpg', 60, 5);

INSERT INTO usuaris (nom, email, contrasenya, nivell_administrador, id_imatge) 
VALUES ('Jonathan', 'jhonlopezramos@gmail.com', '$2y$10$Rx78AjyJDCzvrafEfmeSSOrP1DeQbpAJ3ZiVwNfW9rbB/ZfuBi3HO', 1, 4);
INSERT INTO usuaris (nom, email, contrasenya, nivell_administrador, id_imatge)
VALUES ('Gabriel', 'gabrielgusman@gmail.com', '$2y$10$Rx78AjyJDCzvrafEfmeSSOrP1DeQbpAJ3ZiVwNfW9rbB/ZfuBi3HO', 2, 2);

