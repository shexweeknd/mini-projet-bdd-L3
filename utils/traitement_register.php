<?php
// ini_set("display_errors", 1);
// error_reporting(E_ALL);

include_once("formValidator.php");
include_once("fonction.php");
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$email = $_POST['email'];
$mdpOne = $_POST['passwordOne'];
$mdpTwo = $_POST['passwordTwo'];
$birthdate = $_POST['birthdate'];

// ajout d'un validateur de données avant insertion des données a la base de donnée
$response = array();

$response = validateSigninBirthdate($birthdate);
$response = validatePassword($mdpOne, $mdpTwo);
$response = validateSigninEmail($email);
// $response = validatePrenom($prenom);
// $response = validateNom($nom);

if (empty($response)) {
    $mdp = $mdpTwo;
    $response = insertUser($nom, $prenom, $email, $mdp);

    if ($response['type'] == '1') {
        header("location:../index.php?message=" . $response['status_message'] . "&err=0");
    } else {
        header("location:../index.php?message=" . $response['status_message'] . "&err=1");
    }
} else {
    header("location:../index.php?message=" . $response['status_message'] . "&err=" . $response["err"]);
}
