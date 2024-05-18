<?php

// error_reporting(E_ALL);
// ini_set("display_errors", 1);

include_once "../utils/fonction.php";
require "../utils/database.php";

function getClientInfo($email, $dbh)
{
    //verifier si le client existe dans la table utilisateur
    $query = "SELECT * FROM utilisateur WHERE adresse_email = :email";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $response = $stmt->fetch(PDO::FETCH_ASSOC);
    if (count($response) > 0) {
        return $response;
    }
}

function changePassword($email, $newPassword, $dbh)
{
    // changer le mot de passe si changePassword est appelé

    $newPassword = hashString($newPassword);

    $query = 'UPDATE utilisateur SET mot_de_passe = :password WHERE adresse_email = :email';
    $stmt = $dbh->prepare($query);

    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $newPassword, PDO::PARAM_STR);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}

// code principal pour reinitialiser le mot de passe
try {
    $dsn = "mysql:host=$host;port=$dbport;dbname=$db;charset=utf8mb4";
    if (!$dbh) {
        $dbh = new PDO($dsn, $user, $pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
} catch (PDOException $e) {
    // En cas d'erreur lors de la connexion à la base de données ou de l'exécution de la requête, renvoyez une réponse d'erreur
    echo 'Erreur de base de données : ' . $e->getMessage();
}

if (isset($_POST['op']) && $_POST['op'] == "changePassword") {

    $clientInfo = getClientInfo($_POST['email'], $dbh);
    if (count($clientInfo) <= 0) {
        echo json_encode(array('error' => 'l\'adresse email n\'est pas enregistrée'));
        return;
    }

} else if (isset($_POST['email']) && validateEmail($_POST['email']) && isset($_POST['passwordTwo']) && (strlen($_POST['passwordTwo']) > 0)) {
    //TODEBUG appel de la fonction changePassword()
    if (changePassword($_POST['email'], $_POST['passwordTwo'], $dbh)) {
        echo json_encode(array('success' => "Le mot de passe a ete reinitialise."));
    } else {
        echo json_encode(array('error' => "Le mot de passe n'a pas pu etre reinitialise."));
    }
}