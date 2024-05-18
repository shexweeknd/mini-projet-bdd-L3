<?php
include("../utils/fonction.php");
session_start();
$email = $_POST['email'];
$mdp = $_POST['password'];
$data = isIssetUser($email, $mdp);
if ($data["type"] == '0') {
    header("location:../pages/auth.php?message=" . $data['status_message']);
} else {
    $_SESSION['user_connected'] = $data["data"];
    $_SESSION['smailia'] = $data["data"][0];
    header("location:../pages/" . $_POST['redirect']);
}
