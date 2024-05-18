<?php
ob_start();
include("../utils/db_connect.php");

//fonction de chiffrement
function hashString($str)
{
    // hash du mdp avec l'algorithme sha256
    $hashedStr = hash('sha256', $str);
    return $hashedStr;
}

function issetEmail($email)
{
    global $conn;
    $query = "SELECT * FROM utilisateur";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_array($result)) {
        if ($row['adresse_email'] == $email) {
            return true;
        }
    }
    return false;
}
function insertUser($nom, $prenom, $email, $mdp)
{
    $response = null;
    global $conn;
    $mdp = hashString($mdp);

    $query = "insert into utilisateur(nom, prenom, adresse_email, date_inscription, mot_de_passe) values('" . $nom . "','" . $prenom . "','" . $email . "',NOW(),'" . $mdp . "')";
    if (issetEmail($email) == false) {
        if (mysqli_query($conn, $query)) {
            $response = array(
                'status_message' => 'Inscription reussi, vous pouvez maintenant vous connecter',
                'type' => '1'
            );
        } else {
            $response = array(
                'status_message' => mysqli_error($conn),
                'type' => '0'
            );
        }
    } else {
        $response = array(
            'status_message' => "Inscription non reussi , l'e-mail est deja pris",
            'type' => '0'
        );
    }
    return $response;
}
function isIssetUser($email, $mdp)
{
    global $conn;
    $mdp = hashString($mdp);

    $query = "SELECT * FROM utilisateur where adresse_email='" . $email . "' and mot_de_passe='" . $mdp . "'";
    $response = array();
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_array($result)) {
        $arrayRow = array();
        $arrayRow['user_id'] = $row['user_id'];
        $arrayRow['nom'] = $row['nom'];
        $arrayRow['prenom'] = $row['prenom'];
        $arrayRow['adresse_email'] = $row['adresse_email'];
        $arrayRow['date_inscription'] = $row['date_inscription'];
        $arrayRow['mot_de_passe'] = $row['mot_de_passe'];
        $response[] = $arrayRow;
    }
    $data = null;
    if (empty($response)) {
        $data = array(
            'type' => '0',
            'status_message' => "Ce compte n'existe pas. Veuillez verifier les informations entrées",
            'data' => $response
        );
    } else {
        $data = array(
            'type' => '1',
            'status_message' => "le compte existe",
            'data' => $response
        );
    }
    return $data;
}

function formatDateFr($date)
{
    // Conversion de la date en timestamp
    $timestamp = strtotime($date);

    // Jour de la semaine en français
    $jours = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");

    // Mois en français
    $mois = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");

    // Formatage de la date
    $jour_semaine = $jours[date('w', $timestamp)];
    $jour = date('j', $timestamp);
    $mois_annee = $mois[date('n', $timestamp) - 1];
    $annee = date('Y', $timestamp);

    // Retourner la date formatée
    return "$jour_semaine $jour $mois_annee $annee";
}

function isAdmin($email, $dbh)
{
    $query = "SELECT u.role
    FROM utilisateur u
    WHERE u.email = :email";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userInfo['role'] == 'admin') {
        return true;
    }
    return false;
}

function getUserId($email, $dbh)
{
    $query = "SELECT u.user_id
    FROM utilisateur u
    WHERE u.adresse_email = :email";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    return $userInfo['user_id'];
}

function formatTimeFr($heure)
{
    // Séparation des heures, minutes et secondes
    list($heures, $minutes, $secondes) = explode(':', $heure);

    // Formatage de l'heure
    if ($heures == '00')
        $heure_formattee = "$minutes minutes";
    else
        $heure_formattee = "$heures heures $minutes minutes";

    // Retourner l'heure formatée
    return $heure_formattee;
}

function additionnerTemps($temps1, $temps2)
{
    // Convertir les temps en secondes
    $secondes1 = strtotime("1970-01-01 $temps1 UTC");
    $secondes2 = strtotime("1970-01-01 $temps2 UTC");

    // Additionner les secondes
    $totalSecondes = $secondes1 + $secondes2;

    // Convertir le total des secondes en format "hh:mm:ss"
    $resultat = gmdate("H:i:s", $totalSecondes);

    return $resultat;
}

function validateEmail($email)
{
    $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

    if (preg_match($pattern, $email)) {
        return true;
    } else {
        return false;
    }
}

