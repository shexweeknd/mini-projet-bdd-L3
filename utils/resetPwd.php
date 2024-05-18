<?php

// error_reporting(E_ALL);
// ini_set("display_errors", 1);


include_once "fonction.php";
include_once "sendMail.php";
require "database.php";

function getClientInfo($email, $dbh)
{
    //TOTEST verifier si le client est bien présent dans la liste des users du site

    //verifier si le client existe dans la table users
    $query = "SELECT * FROM users WHERE email = :email";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $response = $stmt->fetch(PDO::FETCH_ASSOC);
    if (count($response) > 0) {
        return $response;
    }
}


function changePassword($email, $newPassword, $dbh)
{
    // changer le mot de passe si changePassword est appelé

    $query = "UPDATE users SET mdp = :password WHERE email = :email";
    $stmt = $dbh->prepare($query);

    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $newPassword, PDO::PARAM_STR);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}


//avant toute chose, on se connecte a la base de donné
try {
    $dsn = "mysql:host=$host;port=$dbport;dbname=$db;charset=utf8mb4";
    if (!$dbh) {
        $dbh = new PDO($dsn, $user, $pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
} catch (PDOException $e) {
    // En cas d'erreur lors de la connexion à la base de données ou de l'exécution de la requête, renvoyez une réponse d'erreur
    echo 'Erreur de base de données : ' . $e->getMessage();
}

if (isset($_POST['op']) && $_POST['op'] == "createAskingMail") {

    $clientInfo = getClientInfo($_POST['email'], $dbh);
    if (count($clientInfo) <= 0) {
        echo json_encode(array('error' => 'l\'adresse email n\'est pas enregistrée'));
        return;
    }

    try {
        $dsn = "mysql:host=$host;port=$dbport;dbname=$db;charset=utf8mb4";
        if (!$dbh) {
            $dbh = new PDO($dsn, $user, $pass);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        //TOTEST pour plus de sécurité, prendre l'ancien mot de passe et créer le hash en conséquence
        $query = "SELECT u.mdp FROM users u WHERE u.email = :email";

        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $stmt->execute();

        $mdp = $stmt->fetch(PDO::FETCH_ASSOC);

        //TOTEST creer le lien de réinitialisation de mot de passe avec le meme style de la déconnexion par lien mail

        $lien = createAskingMail($_POST['email'], $mdp['mdp']);
        if (strlen($lien) <= 0) {
            echo json_encode(array('error' => 'Impossible de créer le lien'));
            return;
        } else {
            //TOTEST prendre le prenom de l'utilisateur concerné
            $body = 'Bonjour ' . ucFirst($clientInfo['prenom']) . ',<p>Vous aviez demandé à réinistialiser votre mot de passe via la plateforme<br/>Cliquez sur le lien suivant pour définir votre nouveau mot de passe: <a href="' . $lien . '">' . $lien . '</a>.</p>SMAILIA vous souhaite une journée scintillante !';
            sendMail($_POST['email'], [], [], [], "SMAILIA - Réinitialisation de mot de passe", $body);
            echo json_encode(array('message' => 'Consultez votre email pour changer de mot de passe.'));
            return;
        }
    } catch (PDOException $e) {
        // En cas d'erreur lors de la connexion à la base de données ou de l'exécution de la requête, renvoyez une réponse d'erreur
        echo json_encode(array('error' => 'Erreur de base de données : ' . $e->getMessage()));
        return false;
    }
} else if (isset($_POST['email']) && validateEmail($_POST['email']) && isset($_POST['passwordTwo']) && (strlen($_POST['passwordTwo']) > 0)) {
    //TODEBUG appel de la fonction changePassword()
    if (changePassword($_POST['email'], $_POST['passwordTwo'], $dbh)) {
        renderSuccess($_POST['email']);
    } else {
        renderFailure("Le mot de passe n'as pu etre changé.");
    }
    //si changePassword return true alors render a successfull message
    //else render failure message 
} else if (isset($_GET['email']) && validateEmail($_GET['email']) && isset($_GET['clientHash'])) {
    // verifier le hash puis donner le formulaire
    //étape 1 verifier si l'utilisateur existe dans la bdd
    $clientInfo = getClientInfo($_GET['email'], $dbh);
    if (count($clientInfo) <= 0) {
        echo json_encode(array('error' => 'l\'adresse email n\'est pas enregistrée'));
        return;
    }

    $dsn = "mysql:host=$host;port=$dbport;dbname=$db;charset=utf8mb4";
    if (!$dbh) {
        $dbh = new PDO($dsn, $user, $pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    //TOTEST pour plus de sécurité, prendre l'ancien mot de passe et créer le hash en conséquence
    $query = "SELECT u.mdp FROM users u WHERE u.email = :email";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':email', $_GET['email'], PDO::PARAM_STR);
    $stmt->execute();

    $mdp = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_GET['clientHash'] != hashString($_GET['email'] . $mdp['mdp'])) {
        echo json_encode(array('error' => 'Lien non valide.'));
        return;
    }

    //etape 3 on lui fournit le formulaire
?>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/shexweeknd.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/jquery-3.5.1.min.js"></script>

    <style>
        .message-container {
            width: -webkit-fill-available;
            height: -webkit-fill-available;
            margin: 10rem 20rem;
            border: 1px solid gray;
            border-radius: 25px;
            padding: 2rem;
            display: inline-flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .message-container .email {
            padding: 0;
            text-align: left;
            padding: .5rem;
            width: 100%;
        }

        .message-container.success {
            background-color: rgb(179, 204, 179, .5);
        }

        .message-container.failure {
            background-color: rgb(254 0 0 / 17%);
        }

        .message-container .error-message {
            color: red;
        }

        .logo-container {
            margin-bottom: 3rem;
        }

        .message-container p {
            font-size: 1.6rem;
            max-width: 70%;
            text-align: center;
        }

        .message-container p a {
            color: blue;
        }

        .message-container button {
            margin-top: 3rem;
        }

        .message-container label {
            width: 100%;
            text-align: left;
            margin-top: 1rem;
        }

        .message-container .input-group input {
            text-align: left;
            padding: .5rem;
        }


        .message-container .input-group .btn {
            margin-top: 0rem;
        }

        .message-container p img {
            max-width: 50%;
        }

        @media screen and (max-width: 980px) {
            .message-container {
                margin: 0;
            }
        }
    </style>

    <form class="message-container success" method="POST" onsubmit="return verifyPassword()" action="https://smailia.fr/utils/resetPwd.php">
        <p class="logo-container">
            <img src="../assets/shexweeknd/smailia_logo.png" alt="SMAILIA" srcset="">
        </p>

        <label for="email">Votre adresse Email: </label>
        <input type="text" class="form-control email" name="email" value="<?php echo $_GET['email']; ?>" readonly>
        <label for="passwordOne">Votre nouveau mot de passe :</label>

        <div class="input-group">
            <input id="passwordOne" type="password" name="passwordOne" class="form-control" placeholder="Huit caractères au minimum" style="margin:0!important" autocomplete="off">
            <button class="btn btn-light" type="button" onclick="togglePasswordVisibility('passwordOne',this)"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z" />
                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829" />
                    <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z" />
                </svg></button>
        </div>
        <label for="passwordTwo">Retaper le nouveau mot de passe :</label>

        <div class="input-group">
            <input id="passwordTwo" type="password" name="passwordTwo" class="form-control" placeholder="Huit caractères au minimum" style="margin:0!important" autocomplete="off">
            <button class="btn btn-light" type="button" onclick="togglePasswordVisibility('passwordTwo',this)"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z" />
                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829" />
                    <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z" />
                </svg></button>
        </div>
        <p id="error-message" style="color: red;margin-bottom: 0;font-size: 1rem;margin-top: 1rem;"></p>
        <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
    </form>

    <script>
        function togglePasswordVisibility(inputId, button) {
            const passwordField = document.querySelector('#' + inputId);
            passwordField.type = passwordField.type === "password" ? "text" : "password";
            if (passwordField.type === "text") {
                $(button).html('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/></svg>');
            } else {
                $(button).html('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/><path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/></svg>');
            }
        }

        function verifyPassword() {
            document.getElementById('error-message').textContent = "";
            passwordOne = document.getElementById('passwordOne').value;
            passwordTwo = document.getElementById('passwordTwo').value;

            if ((passwordOne.length == 0) || (passwordTwo == 0)) {
                document.getElementById('error-message').textContent = "Les champs sont obligatoires.";
                return false;
            }
            if (passwordOne == passwordTwo) {
                return true;
            }

            document.getElementById('error-message').textContent = "Les mots de passe ne correspondent pas.";
            return false;
        }
    </script>
<?php
} else {
    echo "FORBIDDEN...";
}

// fonction qui sert a afficher le succes d'une participation
function renderSuccess($email)
{
    echo '
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/shexWeeknd.css">

    <style>
        .message-container {
            width: -webkit-fill-available;
            height: -webkit-fill-available;
            margin: 10rem;
            border: 1px solid gray;
            border-radius: 25px;
            padding: 2rem;
            display: inline-flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .message-container.success {
            background-color: rgb(179, 204, 179, .5);
        }

        .message-container.failure {
            background-color: rgb(254 0 0 / 17%);
        }

        .message-container .error-message {
            color: red;
        }

        .logo-container {
            margin-bottom: 3rem;
        }

        .message-container p {
            font-size: 1.6rem;
            max-width: 70%;
            text-align: center;
        }

        .message-container p a {
            color: blue;
        }

        .message-container button {
            margin-top: 3rem;
        }

        .message-container p img {
            max-width: 50%;
        }
    </style>

    <form class="message-container success" action="https://smailia.fr/pages/auth.php">
    <p class="logo-container">
        <img src="../assets/shexweeknd/smailia_logo.png" alt="SMAILIA" srcset="">
    </p>
    <p>Votre mot de passe a bien été réinitialisé.</p>
    <button type="submit" class="btn btn-primary">Se connecter</button>
</form>';
    return;
}

// fonction qui sert a afficher le failure d'une désisncription
function renderFailure($errorMessage)
{
    echo '
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/shexWeeknd.css">

    <style>
        .message-container {
            width: -webkit-fill-available;
            height: -webkit-fill-available;
            margin: 10rem;
            border: 1px solid gray;
            border-radius: 25px;
            padding: 2rem;
            display: inline-flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .message-container.success {
            background-color: rgb(179, 204, 179, .5);
        }

        .message-container.failure {
            background-color: rgb(254 0 0 / 17%);
        }

        .message-container .error-message {
            color: red;
        }

        .logo-container {
            margin-bottom: 3rem;
        }

        .message-container p {
            font-size: 1.6rem;
            max-width: 70%;
            text-align: center;
        }

        .message-container p a {
            color: blue;
        }

        .message-container button {
            margin-top: 3rem;
        }

        .message-container p img {
            max-width: 50%;
        }
    </style>
    <form class="message-container failure" action="https://smailia.fr/">
        <p class="logo-container">
            <img src="../assets/shexweeknd/smailia_logo.png" alt="SMAILIA" srcset="">
        </p>
        <p>Echec lors du changement de votre mot de passe veuillez contacter le service client au <a href="tel:01 88 40 17 00"> 01 88 40 17 00</a> ou signalez une anomalie sur la page <a href="https://smailia.fr/pages/bugs.php">Anomalies</a>.<br/><span class="error-message">[' . $errorMessage . ']</span></p>
        <button type="submit" class="btn btn-primary">Accéder au site</button>
    </form>
    ';
    return;
}
