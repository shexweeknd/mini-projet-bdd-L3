<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

include("../utils/fonction.php");
session_start();
$email = $_POST['email'];
$mdp = $_POST['password'];
$data = isIssetUser($email, $mdp);
if ($data["type"] == '0') {
    header("location:../index.php?message=" . $data['status_message']);
} else {
    $_SESSION['user_connected'] = $data["data"];
    $_SESSION['username'] = $data["data"][2];
    header("location:../" . $_POST['redirect']);
}
