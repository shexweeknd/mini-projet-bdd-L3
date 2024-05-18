<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

$server = "localhost";
$username = "new_user";
$password = "new_password";
$db = "message_bdd";
$port = "3306";

$conn =  mysqli_connect($server, $username, $password, $db, $port);


// si vous voulez tester l'application web en local, vous pouvez utiliser:
// 1 - WAMPP sur windows et configurer la base de donnee pour les credentials mentionnés dans le code
// 2 - XAMPP sur linux puis configurer manuellement la bdd pour correspondre aux credentials mentionnés dans le code
