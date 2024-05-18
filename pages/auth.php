<?php
error_reporting(E_ALL);

include_once "elements/header.php";
?>

<!-- protection de la page -->
<?php
$redirect = "pages/acceuil.php";

if (isset($_GET['redirect'])) {
    $redirect = $_GET['redirect'];
}

if (isset($_SESSION['username'])) {
?>
    <script>
        window.location.href = "<?php echo $redirect; ?>"
    </script>
<?php
}
?>

<!--custom page style -->
<style>
    .email-text p {
        color: black;
    }

    /* Proper content */
    .auth-section {
        display: flex;
        flex-redirect: column;
        justify-content: center;
        align-items: center;
        width: 100%;
        top: 30%;
    }

    .auth-section .container {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        flex-redirect: column;
        width: -webkit-fill-available;
    }

    .site-footer {
        width: -webkit-fill-available;
        display: flex;
        justify-content: center;
    }

    .auth-section .title span {
        display: inline-flex;
        font-size: 3rem;
    }

    .auth-content {
        /* overflow-x: hidden; */
        width: 100%;
    }

    .auth-content p {
        color: rgb(43, 42, 42);
        font-size: 1rem;
    }

    /*responsive*/
    @media screen and (max-width: 1280px) {}

    @media screen and (max-width: 980px) {}

    @media screen and (max-width: 736px) {}

    @media screen and (max-width: 430px) {}
</style>

<style>
    .alert {
        animation: appear 5s forwards;
        transition: transform 1s ease-in-out;
        transition: opacity 1s ease-in-out;
    }

    @keyframes appear {
        0% {
            opacity: 0;
            transform: translatey(-20px);
        }

        50% {
            opacity: 1;
            transform: translatey(0px);
        }

        100% {
            opacity: 0;
            transform: translatey(-20px);
        }
    }
</style>

<style>
    .auth-content .auth-nav {
        height: 2.6rem;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 1rem
    }

    .auth-content .auth-nav .con-underline,
    .auth-content .auth-nav .sig-underline {
        width: 0px;
        transition: width .5s ease-in-out;
    }

    .auth-content .auth-nav .con-underline.active,
    .auth-content .auth-nav .sig-underline.active {
        width: 100%;
        border: 1px solid black;
    }

    .auth-content .auth-nav span {
        cursor: pointer;
        color: gray;
        transition: color 0.5s ease-in-out, font-size .5s ease-in-out;
    }

    .auth-content .auth-nav span:hover {
        color: black;
    }

    .auth-content .auth-nav span.active {
        color: black;
        font-size: 1.3rem;
    }

    .auth-content .form-container {
        transition: transform 1.5s ease-in-out;
    }

    .alert {
        position: absolute;
        top: 2rem;
    }
</style>

<section class="auth-section about-section">
    <div class="container mb-5" style="justify-content: center">
        <?php
        $message = $_GET['message'];
        if (isset($message)) { ?>
            <?php
            $message = $_GET['message'];
            $err = $_GET['err'];
            if (isset($message) && ($err != '0')) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php
            } else if (isset($message) && ($err == '0')) { ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php
            }
            ?>
        <?php } ?>
        <div class="auth-content" data-aos="fade-down">
            <div class="auth-nav" style="width: 100%; text-align: center">
                <div style="display: inline-flex; flex-direction: column">
                    <span id="con-nav" class="active">Connexion</span>
                    <div id="con-underline" class="con-underline active"></div>
                </div>
                <div style="display: inline-flex; flex-direction: column">
                    <span id="sig-nav">Inscription</span>
                    <div id="sig-underline" class="sig-underline"></div>
                </div>
            </div>
            <div id="form-container" class="form-container" style="display: flex; justify-content: center; align-items: center; height: 70vh; width: 200%;">
                <div style="width: 100%; display: flex; align-items: center; justify-content: center">
                    <?php
                    include_once "elements/loginForm.php";
                    ?>
                </div>
                <div style="width: 100%; display: flex; align-items: center; justify-content: center">
                    <?php
                    include_once "elements/signinForm.php";
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const conNav = document.getElementById('con-nav');
    const sigNav = document.getElementById('sig-nav');
    const formContainer = document.getElementById('form-container');
    const loginForm = document.getElementById('login-form');
    const signinForm = document.getElementById('signin-form');

    function showCon() {
        conNav.classList.add('active');
        document.getElementById("con-underline").classList.add('active');
        document.getElementById("sig-underline").classList.remove('active');
        sigNav.classList.remove('active');
        loginForm.style.opacity = '1';
        signinForm.style.opacity = '0';
        formContainer.style.transform = 'translate(0%, 0%)';
    }

    function showSign() {
        sigNav.classList.add('active');
        document.getElementById("sig-underline").classList.add('active');
        document.getElementById("con-underline").classList.remove('active');
        conNav.classList.remove('active');
        signinForm.style.opacity = '1';
        loginForm.style.opacity = '0';
        formContainer.style.transform = 'translate(-50%, 0%)'
    }

    conNav.addEventListener('mouseup', showCon);
    sigNav.addEventListener('mouseup', showSign);
    document.getElementById('signin-ref').addEventListener('mouseup', showSign);
    document.getElementById('login-ref').addEventListener('mouseup', showCon);
</script>