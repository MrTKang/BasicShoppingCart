<?php
require("includes/functions.php");
session_start();

if (isset($_POST['submit'])) {
    $_SESSION['user'] = login_user($mysqli, $_POST['password'], $_POST['email'], $_POST['remember_email']);
    if (isset($_SESSION['user']) && isset($_SESSION['cart'])) {
        if (transfer_cart($mysqli, $_SESSION['user']['user_id'], $_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }
    }
}

if (isset($_GET['email']) && isset($_GET['key'])) {
    confirm_user_email($mysqli, $_GET['email'], $_GET['key']);
}

$user_email = "";
if (isset($_COOKIE['login'])) {
    $user_email = $_COOKIE['login'];
}

if (isset($_SESSION['user']) && isset($_POST['directurl'])) {
    $directurl = "Location: ";
    $directurl.= $_POST['directurl'];
    header($directurl);
}
?>

<!-- TODO: reCaptcha -->
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Login</title>
        <link href="css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <div class="login-container">
            <form class="login-form" method="post" action="login.php">
                <h1>
                <?php 
                    if (isset($_GET['login-required'])) {
                        echo("Please login first");
                    } 
                ?></h1>
                <label>Email</label>
                <input type="email" name="email" class="form-control" required="" value="<?php echo $user_email ?>" autofocus="">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required="">
                <input type="hidden" name="directurl" 
                value="<?php
                if (isset($_GET['login_required'])) {
                    echo($_GET['login_required']);
                } else {
                    echo('index.php');
                }
                ?>" class="form-control" required="">
                <div class="checkbox mb-3">
                    <label>
                        <input type="checkbox" name="remember_email" value="remember_email"> Remember me
                    </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block" name="submit" >Sign in</button>
                <a href="signup.php">Sign Up</a>
                <a href="forgotpassword.php" class="forgot-password">Forgot Password</a>
                <p class="mt-5 mb-3 text-muted">© 2017-2018</p>
            </form>
        </div>
    </body>
</html>