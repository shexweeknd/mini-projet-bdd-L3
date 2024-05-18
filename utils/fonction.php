<?php
ob_start();
include("../utils/db_connect.php");
function issetEmail($email)
{
    global $conn;
    $query = "SELECT * FROM users";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_array($result)) {
        if ($row['email'] == $email) {
            return true;
        }
    }
    return false;
}
function insertUser($nom, $prenom, $email, $mdp, $birthdate)
{
    $response = null;
    global $conn;
    $query = "insert into users(nom, prenom, email, creation_date, mdp, birthday) values('" . $nom . "','" . $prenom . "','" . $email . "',NOW(),'" . $mdp . "','" . $birthdate . "')";
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
    $query = "SELECT * FROM users where email='" . $email . "' and mdp='" . $mdp . "'";
    $response = array();
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_array($result)) {
        $arrayRow = array();
        $arrayRow['id'] = $row['id'];
        $arrayRow['nom'] = $row['nom'];
        $arrayRow['prenom'] = $row['prenom'];
        $arrayRow['birthday'] = $row['birthday'];
        $arrayRow['email'] = $row['email'];
        $arrayRow['creation_date'] = $row['creation_date'];
        $arrayRow['mdp'] = $row['mdp'];
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
function getActivity()
{
    global $conn;
    $query = "SELECT * FROM activity";
    $response = array();
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_array($result)) {
        $arrayRow = array();
        $arrayRow['id'] = $row['id'];
        $arrayRow['name'] = $row['name'];
        $arrayRow['description'] = $row['description'];
        $arrayRow['imgpath'] = $row['imgpath'];
        $response[] = $arrayRow;
    }

    return $response;
}
function getEvent($id)
{
    global $conn;
    $query = "SELECT * FROM events where activityId='" . $id . "'";
    $response = array();
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_array($result)) {
        $arrayRow = array();
        $arrayRow['id'] = $row['id'];
        $arrayRow['activityId'] = $row['activityId'];
        $arrayRow['subject'] = $row['subject'];
        $arrayRow['description'] = $row['description'];
        $arrayRow['date'] = $row['date'];
        $arrayRow['time'] = $row['time'];
        $arrayRow['duration'] = $row['duration'];
        $arrayRow['imgpath'] = $row['imgpath'];
        $response[] = $arrayRow;
    }

    return $response;
}

function getEventInfo($eventId, $dbh)
{
    $query = "SELECT e.id, a.name AS activityName, e.subject, e.description, e.date, e.time, e.duration, e.imgpath, e.state, COUNT(p.id) AS participants
            FROM events e 
            INNER JOIN activity a ON e.activityId = a.id
            LEFT JOIN participations p ON e.id = p.eventId
            WHERE e.id = :eventId";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
    $stmt->execute();

    $eventData = $stmt->fetch(PDO::FETCH_ASSOC);
    return $eventData;
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
    FROM users u
    WHERE u.email = :email";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    // Récupération des résultats dans un tableau
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userInfo['role'] == 'admin') {
        return true;
    }
    return false;
}

function getUserId($email, $dbh)
{
    $query = "SELECT u.id
    FROM users u
    WHERE u.email = :email";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    // Récupération des résultats dans un tableau
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    return $userInfo['id'];
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

function validerLienSkype($lien)
{
    $regex = '/\bhttps?:\/\/(?:join\.skype\.com\/)[a-zA-Z0-9-]+\b/';
    return preg_match($regex, $lien);
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


function giveTicketNumber($ticketId, $date_soumission)
{
    // Convertir la date en format JJ-MM-AA
    $formattedDate = date('d-m-y', strtotime($date_soumission));

    // Créer le numéro de ticket en concaténant les éléments
    $ticketNumber = "REPORT-$formattedDate-$ticketId";

    return $ticketNumber;
}

//fonction de chiffrement
function hashString($str)
{
    // Utilisez la fonction hash() avec l'algorithme MD5
    $hashedStr = hash('sha256', $str);
    return $hashedStr;
}

function generateLink($email, $mdp, $eventId)
{
    $result = "https://smailia.fr/utils/unparticipate.php?email=" . urlencode($email) . "&clientHash=" . hashString($email . $mdp) . "&eventId=" . $eventId;

    return $result;
}

function generateParticipationLink($email, $mdp, $eventId)
{
    $result = "https://smailia.fr/utils/participate.php?email=" . urlencode($email) . "&clientHash=" . hashString($email . $mdp) . "&eventId=" . $eventId;

    return $result;
}

function createAskingMail($email, $mdp)
{
    // faire en sorte que le mail de reset soit crée puis envoi du mail sur le mail du client
    $result = "https://smailia.fr/utils/resetPwd.php?email=" . urlencode($email) . "&clientHash=" . hashString($email . $mdp);

    return $result;
}
