<?php
session_start(); // Démarrer la session

// Détruire toutes les variables de session
$_SESSION = array();

// Si vous souhaitez détruire complètement la session, effacez également le cookie de session.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Enfin, détruisez la session.
session_destroy();

header('Location: login.php'); // Rediriger vers la page de connexion
exit;
