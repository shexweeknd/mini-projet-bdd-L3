<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
        if (isset($_SESSION['username'])) {
            $display = $_SESSION['username'];
        } else {
            $display = 'Authentification';
        }
        echo $display . ' | canal de messagerie'
        ?>
    </title>
    <link rel="shortcut icon" type="image/png" href="assets/img/logo-mail.png" />
    <link rel="stylesheet" href="../assets/css/shexweeknd.css">

    <link href="../assets/css/aos-2.3.1.css" rel="stylesheet">
    <script src="../assets/js/aos-2.3.1.js"></script>

    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="../assets/js/bootstrap-5.0.2.js"></script>

    <script src="../assets/js/jquery-3.6.0.min.js"></script>
</head>