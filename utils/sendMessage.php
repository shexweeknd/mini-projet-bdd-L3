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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['userId']) && isset($_POST['message'])) {
    $userId = $_POST['userId'];
    $contenu = $_POST['message'];
    $dateEnvoi = date('Y-m-d H:i:s');
} else {
    echo json_encode(array('error' => 'opération interdite des données non conformes...'));
    exit;
}

//etape 1, verifier si l'user_id est bien dans la table utilisateur
$query = "SELECT * FROM utilisateur u WHERE u.user_id = :user_id";

$stmt = $dbh->prepare($query);
$stmt->bindParam(':user_id', $userId);
$stmt->execute();

$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!isset($userInfo)) {
    echo json_encode(array('error' => 'opération interdite des données non conformes...'));
    return;
}

// envoi du message dans la base de donnée
$query = "INSERT INTO message (contenu, date_envoi, expediteur) VALUES (:contenu, :date_envoi , :user_id)";

$stmt = $dbh->prepare($query);
$stmt->bindParam(':contenu', $contenu, PDO::PARAM_STR);
$stmt->bindParam(':date_envoi', $dateEnvoi);
$stmt->bindParam(':user_id', $userId);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    echo json_encode(array('error' => 'Le message n\'a pas été envoyé...'));
    return;
} else {
    echo json_encode(array('success' => 'Message envoyé'));
    return;
}
