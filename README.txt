(Afegit)Pràctica 5
•	Afegida una nova vista, en aquest cas vista.canviContUser.php amb la que l’usuari pot accedir des de l’apartat compres un cop logat.
•	Aquesta vista demanarà l’antiga contrasenya i demanarà dos cops per a la que es vol canviar.
•	Per tal d’augmentar la seguretat de les contrasenyes s’ha establert que aquestes com a mínim tinguin 8 caràcters i a més, 
        han de contenir números, lletres i caràcters especials.

S’ha comprovat que la forma de logar amb la funció que es troba a vista.connexio.php funciona de la forma correcta, 
ja que reinicialitza el comptador cada cop que s’accedeix a la base de dades per alguna cosa.

S’està establint de la mateixa manera a tots els documents de la vista, una forma de que si l’usuari no té la sessió iniciada, 
reenvií al formulari login. D’aquesta forma s’aconsegueix tenir una mica de seguretat de que l’usuari no pot establir la connexió a cap document 
si no està logat.

S’ha canviat la localització dels errors i encerts de les funcions, per tal de mostrar-les una mica millor, 
ara es poden visualitzar a una finestra que es troba a la dreta(logout-container).
•	S’ha afegit al codi de model.login.php el recaptcha v2 corresponent, aquest, mira si els intents son superiors a 3, 
        per tal de que un cop entres tres cops la contrasenya malament, al quart intent es mostrarà el recaptcha corresponent. 
        Aquesta acció s’ha realitzat amb combinació de codi, php i javascript.
•	S’ha creat una nova carpeta on establir les keys de server i client en una variable de sessió per tal de donar una seguretat a les keys correstponents.

S’ha reescrit part del codi de model.registre.php per tal de que carregui la variable de sessió $usuariId = $pdo->lastInsertId(); $_SESSION['usuari_id'] = $usuariId; 
i que la pàgina vista.formulari.php no redireccioni en el registre a la pàgina vista.formularilogin.php.

S’ha canviat el document model.oblitcontra.php, per tal de que les select es facin amb la id de l’usuari en comptes del per el correu, 
llavors el que fem es realitzar les selects corresponents per tal de veure la id de l’usuari al que correspon el correu.


S’ha rectificat el codi corresponent de la base de dades per tal d’utilitzar un altre taula per el token del remember me.
(Encara no funciona s’ha de repasar el codi)

S’han canviat a més les dades de la mateixa per tal de que una taula s’encarregui de la gestió de tokens per a l’inici de la sessió recordant la contrasenya.

Per tal de dividir codi ara cont.processar.php, s’ha traslladat part del codi i funcions a cont.articles.php, aquest, 
s’encarregarà ara de mostrar els articles d’una forma o un altre, a més, cridarà a cont.processar.php que conté la connexió a la base de dades i l’startsession, 
a vista.formulari.php es farà la crida a cont.articles.php.

Ja es possible mostrar articles de forma ascendent i descendent, a més de buscar i mostrar un sol article. Vista.formulari.php crida a cont.articles.php, 
i aquest retorna les variables carregades al nom de sessió corresponent a vista.formulari.php, aquest crida a les funcions corresponents per tal de mostrar el demanat, ja sigui cerca per nom, 
o mostrar per ordre ascendent o descendent(nom).

S’afegeix també una forma d’esborrar l’article comprat corresponent a vista.veureCompres.php, crida amb un formulari a cont.elimarCompra.php, 
aquest s’encarrega d’accedir a la base de dades per tal de que esborri l’article corresponent a traves de l’id corresponent, 
ja que d’un altre forma esborraria possiblement compres d’un altre usuari(per exemple).

Per tal de fer més fàcil l’ús de la pàgina, s’ha creat una redirecció amb un index.php per tal de redirigir automàticament a vista.formulari.php, 
d’aquesta manera no s’ha de configurar res.

S’ha pujat la nova versió a donDominio:
https://www.jlopez5.cat/

a data de 14/11/2024 s’ha rectificat el codi per tal de que el MVC estigui on toca. 
S’han rectificat noms per tal de que estiguin tots amb la mateixa construcció, nova paraula dins del nom, amb majúscules.
S’ha afegit diversos directoris git per tal de fer la pujada dels arxius mitjançant un token.

Ampliació de vites i constants, s’ha afegit una vista.administrador.php, aquesta nomes es accessible per un usuari de nivell 1, 
s’utilitza una variable de sessió per aquesta tasca. Aquesta vista utilitza una llista que es crida al entrar a un document anomenat 
cont.administracioUsuaris.php, a on l’administrador te privilegis per esborrar.

La taula usuaris a sigut modificada també per a realitzar aquesta tasca, s’ha creat una columna nova anomenada nivell_administrador en la que es un int, 
el valor per defecte serà 2, per tant el nivell 1 serà administrador i l’altre usuari pla.


