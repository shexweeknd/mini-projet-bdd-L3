<?php
error_reporting(E_ALL);

include_once "elements/header.php";

// Redirection vers la page de connexion si non connecté
if (!isset($_SESSION['user_connected'])) {
    header('Location: index.php');
    exit;
}
?>

<style>
    *,
    .app-title h1 {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, Helvetica, sans-serif;
    }

    .app-title {
        display: flex;
        justify-content: space-between;
        background-color: #333;
        color: #75eaff;
        font-size: 2.1rem;
        padding: 15px;
    }

    .app-title h1 {
        color: #75eaff;
        font-weight: bold;
    }

    .bienvenu {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        font-size: 1.8rem;
        color: white;
        margin: 0
    }

    .message-ji {
        padding: 1rem 100px 1rem 100px;
        height: 70vh;
        width: 100vw;
    }

    .message-ji::before {
        content: "";
        display: flex;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, .3);
        border: 1px solid gray;
        border-radius: 5px
    }

    .message-ch {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .message-ch form input {
        width: 70vw;
        height: 54px;
        margin-bottom: 20px;
        font-size: 1.2rem;
        padding-left: 25px;
        border-radius: 50px;
        border: solid #21bfdb;

    }

    img {
        width: 50px;
        height: auto;
    }

    .init {
        position: relative;
        z-index: 1;
        bottom: 40px;
        left: 20px;
    }

    .mess {
        margin-top: 8px;
        font-size: 1.1rem;
    }

    .bouton {
        position: relative;
        right: 51.5px;
        top: 6.25px;
    }

    .cerc img {
        border: black 2.5px solid;
        border-radius: 50%;
        object-fit: contain;
    }


    .env img {
        width: 25px;
    }

    .env {
        position: relative;
        bottom: 40px;
        left: 10px;
    }
</style>

<style>
    .userspace {
        display: inline-flex;
        flex-direction: row;
        gap: 1rem;
    }

    .dicsonnect-button {
        width: 50px;
        height: auto;
        cursor: pointer;
    }

    .message-wrapper {
        width: auto;
        max-height: -webkit-fill-available;
        display: inline-flex;
        flex-direction: column;
        padding: 2rem 0 0 0;
    }
</style>

<section>
    <div class="app-title">
        <h1>Yoyo Chat</h1>
        <div class="userspace">
            <p class="bienvenu"><?php echo $_SESSION["username"] ?></p>
            <div class="dicsonnect-button">
                <img src="../assets/img/switch.png" />
            </div>
        </div>
    </div>
    <div class="message-wrapper">
        <div class="message-ji">
            <?php include_once "../elements/message_left.php" ?>
        </div>
        <div class="message-ch">
            <div>
                <form action="">
                    <input type="text" placeholder="Message">
                </form>
            </div>
            <div class="bouton">
                <div class="cerc"><img src="../assets/img/cercle (2).png" alt=""></div>
                <div class="env"><img src="../assets/img/envoyer.png" alt=""></div>
            </div>
        </div>
    </div>
</section>