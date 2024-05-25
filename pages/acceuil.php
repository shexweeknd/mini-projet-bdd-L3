<?php
// Redirection vers la page de connexion si non connecté
if (!isset($_SESSION['user_connected'])) {
    header('Location: index.php');
    exit;
}
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, Helvetica, sans-serif;
    }

    .title {
        display: flex;
        justify-content: center;
        background-color: #333;
        color: #75eaff;
        font-size: 2.1rem;
        padding: 15px;
    }

    .bienvenu {
        display: flex;
        justify-content: center;
        margin-top: 50px;
        font-size: 1.8rem;
    }

    .message-ji {

        margin: 50px 100px 50px 100px;
    }

    .message-so {
        margin-top: 30px;
        font-size: 1.1rem;

    }

    .date_heure {
        display: flex;
        justify-content: end;
        margin-top: 8px;
        font-size: 0.7rem;
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

    .message-so {
        display: flex;
        flex-direction: row;
        gap: 15px;
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

<section>
    <div class="title">
        <h1>Yoyo Chat</h1>
    </div>
    <p class="bienvenu">Bienvenu <?php echo $_SESSION["username"] ?></p>
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
</section>