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

    .message-container {
        display: inline-flex;
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
        border: 1px solid #21bfdb;

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

    .send-message {
        position: relative;
        cursor: pointer;
        right: 51.5px;
        top: 6.25px;
        scale: 1;
        transition: all .5s ease-in-out;
    }

    .send-message:hover {
        scale: 1.1;
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

    .dicsonnect-button>img {
        scale: .8;
        transition: scale .5s ease-in-out;
    }

    .dicsonnect-button>img:hover {
        scale: .9;
    }

    .message-wrapper {
        width: auto;
        max-height: -webkit-fill-available;
        display: inline-flex;
        flex-direction: column;
        padding: 2rem 0 0 0;
    }

    .message-container {
        display: inline-flex;
        flex-direction: column;
        overflow-y: auto;
    }
</style>

<style>
    .message-so {
        font-size: 1rem;
        display: flex;
        flex-direction: row;
        gap: 15px;
        height: 100%;
    }

    .mess1 {
        width: 100%;
        padding: 1rem;
    }

    .mess1 .message-owner {
        font-size: medium;
    }

    .mess1 .message-value {
        margin-top: 0px;
        font-size: 1rem;
        background-color: rgba(0, 0, 255, .7);
        color: white;
        border-radius: 25px;
        padding: 10px;
        width: fit-content;
    }
</style>

<section>
    <div class="app-title">
        <h1>Yoyo Chat</h1>
        <div class="userspace">
            <p class="bienvenu"><?php echo $_SESSION["username"] ?></p>
            <div class="dicsonnect-button">
                <img src="../assets/img/exit.png" />
            </div>
        </div>
    </div>
    <div class="message-wrapper">
        <div class="message-ji">
            <div class="message-container">
            </div>
        </div>
        <div class="message-ch">
            <div>
                <form id="message-form" action="">
                    <input id="message-to-send" class="form-control" type="text" placeholder="Message">
                </form>
            </div>
            <div type="submit" class="send-message">
                <div class="cerc"><img src="../assets/img/cercle (2).png" alt=""></div>
                <div class="env"><img src="../assets/img/envoyer.png" alt=""></div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {

        document.getElementById('message-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Empêche le rechargement de la page
            sendMessage(); // Appelle la fonction sendMessage
        });

        //deconnexion de l'utilisateur
        $('.dicsonnect-button').click(function() {
            $.ajax({
                url: '../utils/logout.php', // The URL to the PHP file
                success: function(response) {
                    console.log("déconnexion...");
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log("aucune session correspondante...");
                    window.location.href = "../index.php";
                }
            });
        });

        function sendMessage() {
            var contenu = document.querySelector("#message-to-send").value;
            $.ajax({
                url: '../utils/sendMessage.php',
                method: "POST",
                data: {
                    message: contenu,
                    userId: "<?php echo $_SESSION['user_connected']['user_id'] ?>",
                },
                success: function(response) {
                    //TODO ajouter en conséquence un message Right dans le container messsage-container
                    console.log(response);
                    document.querySelector("#message-to-send").value = "";
                    return;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    //TODO afficher un display error message qui reste permanent lorsqu'on ne recharge pas la page HTML
                    alert("message non envoyé...");
                }
            });
        }
        //envoi de message de l'utilisateur
        $('.send-message').click(sendMessage);

        //prendre tous les messages dans la base de donnée
        var allMessages = new Array();
        var isFetching = false;
        var retryCount = 0;
        var maxRetries = 5;

        function displayNotification(message, type) {
            alert(message);
        }

        function scrollToLatestMessage() {
            const messageContainer = document.querySelector('.message-container');
            const lastMessage = messageContainer.lastElementChild;
            if (lastMessage) {
                lastMessage.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }

        function supplyMessageContainer(allNewMessages) {
            const fetchPromises = allNewMessages.map(item => {
                var elemToFetch = "message_left.php";

                if (item.expediteur == "<?php echo $_SESSION['user_connected']['user_id'] ?>") {
                    elemToFetch = "message_right.php";
                }

                return fetch("elements/" + elemToFetch).then(response => response.text()).then(data => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data, 'text/html');

                    // Modifiez le contenu du message en utilisant item
                    const messageValue = doc.querySelector('.message-value');
                    if (messageValue) {
                        messageValue.textContent = item.contenu;
                    }

                    const avatarContent = doc.querySelector('.avatar-content');
                    if (avatarContent) {
                        avatarContent.textContent = item.prenom.charAt(0).toUpperCase();
                    }

                    const messageOwner = doc.querySelector('.message-owner');
                    if (messageOwner) {
                        messageOwner.textContent = item.prenom + ' ' + item.nom;
                    }

                    const dateHeure = doc.querySelector('.date_heure');
                    if (dateHeure) {
                        dateHeure.textContent = item.date_envoi;
                    }

                    const messageElement = doc.body.firstChild;
                    messageElement.id = `message-${item.message_id}`;

                    return messageElement;
                });
            });

            return Promise.all(fetchPromises).then(elements => {
                const messageContainer = document.querySelector('.message-container');
                elements.forEach(element => {
                    messageContainer.appendChild(element);
                });

                scrollToLatestMessage();
            });
        }

        function queryAllMessages() {
            var user_id = "<?php echo $_SESSION['user_connected']['user_id'] ?>";
            console.log("demande tous les messages...");
            $.ajax({
                url: '../utils/getMessages.php',
                method: "POST",
                data: {
                    op: "all",
                    userId: user_id,
                },
                success: function(response) {
                    console.log(response);
                    allMessages = JSON.parse(response);
                    supplyMessageContainer(allMessages).then(() => {
                        retryCount = 0;
                        startPolling();
                    });
                    return;
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    displayNotification("Erreur de connexion. Réessai...", "error");
                    if (retryCount < maxRetries) {
                        retryCount++;
                        setTimeout(queryAllMessages, 2000);
                    } else {
                        alert("Il n'y a aucun message dans la base de donnée...");
                    }
                }
            });
        }

        function queryLastMessages(lastMessageId) {
            if (isFetching) return;
            isFetching = true;

            var user_id = "<?php echo $_SESSION['user_connected']['user_id'] ?>";
            console.log("demande les derniers messages apres " + lastMessageId + "...");
            $.ajax({
                url: '../utils/getMessages.php',
                method: "POST",
                data: {
                    op: "after",
                    userId: user_id,
                    lastMessageId
                },
                success: function(response) {
                    isFetching = false;
                    lastMessages = JSON.parse(response);
                    if (lastMessages['error']) {
                        console.log(lastMessages['error']);
                        return;
                    } else {
                        supplyMessageContainer(lastMessages).then(() => {
                            console.log("nouveaux messages recus...");
                            retryCount = 0;
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    //TODO afficher un display error message qui reste permanent lorsqu'on ne recharge pas la page HTML
                    isFetching = false;
                    displayNotification("Erreur de connexion. Réessai...", "error");
                    if (retryCount < maxRetries) {
                        retryCount++;
                        setTimeout(() => {
                            queryLastMessages(lastMessageId);
                        }, 2000);
                    } else {
                        alert("Il n'y a aucun message dans la base de donnée...");
                    }
                }
            });
        }

        function getLatestMessageId() {
            const messageContainer = document.querySelector('.message-container');
            const messages = messageContainer.children;

            if (messages.length === 0) {
                return null;
            }

            const lastMessage = messages[messages.length - 1];

            const lastMessageId = lastMessage.id;

            const numericId = lastMessageId.split('-')[1];

            return numericId;
        }

        function startPolling() {
            // console.log(getLatestMessageId());
            setInterval(() => {
                queryLastMessages(getLatestMessageId());
            }, 500);
        }

        queryAllMessages();
    });
</script>