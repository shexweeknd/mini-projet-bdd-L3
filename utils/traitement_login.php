<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);
session_start();

include("../utils/fonction.php");
$email = $_POST['email'];
$mdp = $_POST['password'];
$data = isIssetUser($email, $mdp);
if ($data["type"] == '0') {
    header("Location: ../index.php?message=" . $data['status_message']);
} else {
    $_SESSION['user_connected'] = $data["data"][0];
    $_SESSION['username'] = $_SESSION["user_connected"]["prenom"] . " " . $_SESSION['user_connected']["nom"];
    header("Location: ../index.php");
}
