<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<body class="bg">
    <?php
    if (!isset($_SESSION['user_connected'])) {
        include_once 'pages/auth.php';
    } else {
        include_once 'pages/acceuil.php';
    }
    ?>
</body>

</html>

<script>
    AOS.init({
        duration: 1500,
    });
</script>