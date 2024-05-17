<style>
    .login-form {
        display: inline-flex;
        margin: 10px;
        flex-direction: column;
        width: 70%;
        padding: 2em;
        transition: opacity .5s ease-in-out;
    }

    .login-form a {
        color: #167ac6;
        text-decoration: underline;
        background-color: transparent;
        -webkit-text-decoration-skip: objects;
    }

    .login-form a:hover {
        color: #62b1bc;
    }

    .login-form h2 {
        font-family: "Nunito Sans", -apple-system, BlinkMacSystemFont, "Segoe UI",
            Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji",
            "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        font-weight: 700;
        font-size: xx-large;
    }

    .login-form label,
    .login-form input[type=text],
    .login-form input[type=password] {
        color: rgb(43, 42, 42);
        width: auto;
        margin: 0;
        margin-bottom: .5rem;
    }

    .login-form input {
        /* border: 1px solid rgb(43, 42, 42); */
        border: var(--bs-border-width) solid var(--bs-border-color);
        text-align: left;
        padding: 5px;
        font-size: medium;
    }

    .login-form input[type=checkbox] {
        margin-right: 5px;
    }

    .login-form button {
        border: var(--bs-border-width) solid var(--bs-border-color);
    }

    .login-form #submit {
        align-self: center;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    @media screen and (max-width: 1024px) {
        .login-form h2 {
            font-size: x-large;
        }
    }
</style>


<form id="login-form" class="login-form" action="../utils/traitement_login.php" method="post">

    <div style="display: inline-flex; flex-direction: column; text-align: center; justify-content: center; align-items: center; margin-bottom: 15px">
        <h2>Connectez-vous</h2>
    </div>
    <input style="display: none" name="redirect" value="<?php echo $redirect; ?>"></input>
    <label for="email">Email:</label>
    <input class="form-control" type="text" id="email" name="email" placeholder="Ex: exemple@email.com">
    <label for="password">Mot de passe :</label>
    <div class="input-group">
        <input id="password" type="password" name="password" class="form-control" placeholder="Huit caractères au minimum" style="margin:0!important">
        <button class="btn btn-light" type="button" onclick="togglePasswordVisibility('password',this)"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z" />
                <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829" />
                <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z" />
            </svg></button>
    </div>
    <div style="display: inline-flex; padding: 5px;">
        <input id="remeber" name="remember" type="checkbox">
        <p style="margin: unset; font-size: medium" for="remember">Se souvenir</p>
    </div>
    <button type="submit" id="submit" style="justify-self: center" class="button-5">Se connecter</button>

    <p style="margin: unset; font-size: medium; align-self: center">
        Pas encore de compte ?
        <a href="#" id="signin-ref">Cliquez ici</a>
    </p>
    <p style="margin: unset; font-size: medium; align-self: center">
        Mot de passe oublié ?
        <a href="reset.php">Réinitialisez-le ici</a>
    </p>
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
</script>