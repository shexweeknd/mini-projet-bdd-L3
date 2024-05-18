<?php
ob_start();
// ce fichier contiendra les validateurs des formulaires d'inscriptions

//le nom doit avoir plus de 4 caracteres et doit etre seulement en Alphabétique
function validateNom($nom)
{
    $response = array();

    if (!strlen($nom)) {
        $response["status_message"] = "Le nom ne doit pas etre vide";
        $response["err"] = '2';
        return $response;
    }
    if (!ctype_alpha($nom)) {
        $response["status_message"] = "Le nom précisé dans le formulaire doit etre uniquement en alphabétique";
        $response["err"] = '2';
        return $response;
    }
    if (strlen($nom) < 4) {
        $response['status_message'] = "Le enom est trop court (au moins 4 lettres)";
        $response['err'] = '2';
        return $response;
    }
    return $response;
}

//le prenom doit avoir plus de 4 caracteres et doit etre seulement en Alphabétique
function validatePrenom($prenom)
{
    $response = array();

    if (!strlen($prenom)) {
        $response["status_message"] = "Le prenom ne doit pas etre vide";
        $response["err"] = '2';
        return $response;
    }
    if (!ctype_alpha($prenom)) {
        $response["status_message"] = "Le prenom précisé dans le formulaire doit etre uniquement en alphabétique";
        $response["err"] = '2';
        return $response;
    }
    if (strlen($prenom) < 4) {
        $response['status_message'] = "Le prenom est trop court (au moins 4 lettres)";
        $response['err'] = '2';
        return $response;
    }
    return $response;
}

//l'email doit avoir un format "regex" valide
function validateSigninEmail($email)
{
    $response = array();

    if (!strlen($email)) {
        $response["status_message"] = "L'email ne doit pas etre vide";
        $response["err"] = '3';
        return $response;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["status_message"] = "L'email n'est pas valide";
        $response["err"] = "3";
    }
    return $response;
}

function validateSigninBirthdate($birthdate)
{
    $response = array();

    if (!strlen($birthdate)) {
        $response["status_message"] = "La date de naissance ne doit pas etre vide";
        $response["err"] = '4';
        return $response;
    }
    // Expression régulière pour valider une date au format MM/JJ/AAAA
    $regex = '~^(0[1-9]|1[0-2])/(0[1-9]|[12][0-9]|3[01])/\d{4}$~';

    // Vérifier si la date de naissance correspond au format attendu
    if (!preg_match($regex, $birthdate)) {
        $response["status_message"] = "La date de naissance n'est pas au bon format";
        $response["err"] = '4';
        return $response;
    }

    // Extraire le mois, le jour et l'année de la date de naissance
    list($month, $day, $year) = explode('/', $birthdate);

    // Calculer l'âge en années
    $age = date('Y') - $year;

    // Si l'anniversaire de la personne n'est pas encore passé cette année, réduire l'âge de 1
    if (date('md') < sprintf('%02d%02d', $month, $day)) {
        $age--;
    }

    // Vérifier si l'âge est inférieur à 18 ans
    if ($age < 18) {
        $response["status_message"] = "Vous devez etre agé de 18 ans ou plus pour accéder aux services";
        $response["err"] = '4';
        return $response;
    }

    // Si la date de naissance est valide et que la personne a au moins 18 ans, retourner un array vide
    return $response;
}

//les mots de passe doivent etre plus de 08 caracteres et doit correspondre
function validatePassword($mdpOne, $mdpTwo)
{
    $response = array();

    if (!strlen($mdpOne) || !strlen($mdpOne)) {
        $response["status_message"] = "Les mot de passe ne doivent pas etre vide";
        $response["err"] = '2';
        return $response;
    }
    if ((strlen($mdpOne) < 8) || (strlen($mdpTwo) < 8)) {
        $response["status_message"] = "Le mot de passe est trop court (8 caracteres au minimum)";
        $response["err"] = "4";
        return $response;
    }
    if (strcmp($mdpOne, $mdpTwo)) {
        $response["status_message"] = "Les mots de passe ne correspondent pas";
        $response["err"] = "4";
        return $response;
    }
    return $response;
}
