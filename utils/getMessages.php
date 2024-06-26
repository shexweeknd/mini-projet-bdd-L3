<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

include_once "../utils/database.php";

try {
    $dsn = "mysql:host=$host;port=$dbport;dbname=$db;charset=utf8mb4";
    $dbh = new PDO($dsn, $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur lors de la connexion à la base de données ou de l'exécution de la requête, renvoyez une réponse d'erreur
    echo 'Erreur de base de données : ' . $e->getMessage();
    return;
}
$userId = $_POST['userId'];
$operation = $_POST['op'];

// etape 1, verifier si l'user_id est bien dans la table utilisateur
$query = "SELECT * FROM utilisateur u WHERE u.user_id = :user_id";

$stmt = $dbh->prepare($query);
$stmt->bindParam(':user_id', $userId);
$stmt->execute();

$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!isset($userInfo) || !isset($operation)) {
    echo json_encode(array('error' => 'opération interdite a cause de données non conformes...'));
    return;
}

function sendAllMessages($dbh)
{
    // query des messages dans la base de donnée
    $query = "SELECT m.message_id, m.date_envoi, m.expediteur, m.contenu, u.nom, u.prenom
    FROM message m 
    INNER JOIN utilisateur u ON m.expediteur = u.user_id 
    ORDER BY m.date_envoi ASC";

    $stmt = $dbh->prepare($query);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        echo json_encode(array('error' => 'Aucun message n\'est enregistré dans la table...'));
        return;
    } else {
        $allMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($allMessages);
        return;
    }
}

function sendLatestMessages($dbh, $lastMessageId)
{
    // query des derniers messages dans la base de donnée
    $query = "SELECT m.message_id, m.date_envoi, m.expediteur, m.contenu, u.nom, u.prenom
    FROM message m 
    INNER JOIN utilisateur u ON m.expediteur = u.user_id 
    WHERE message_id > :lastMessageId
    ORDER BY m.date_envoi ASC";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':lastMessageId', $lastMessageId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        echo json_encode(array('error' => 'Aucun nouveau message dans la table...'));
        return;
    } else {
        $latestMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($latestMessages);
        return;
    }
}

if ($operation == "all") {
    sendAllMessages($dbh);
} else if ($operation == "after" && isset($_POST['lastMessageId'])) {
    sendLatestMessages($dbh, $_POST['lastMessageId']);
} else {
    echo json_encode(array('error' => 'opération interdite a cause de données non conformes...'));
    return;
}
