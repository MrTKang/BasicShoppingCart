<?php
require("includes/connection.php");
session_start();

if (isset($_POST['submit'])) {
    $select_user = "SELECT * FROM users WHERE email = '";
    $select_user.= $_POST['email'];
    $select_user.= "'";

    $select_user_result = $mysqli->query($select_user);
    if ($select_user_result->num_rows == 1) {
        $login_user = $select_user_result->fetch_array();

        if ($login_user['password'] === md5($_POST['password']) && $login_user['email_confirmed'] == 1) {
            $_SESSION['user'] = $login_user;
            header("Location: index.php");
        } else if ($login_user['password'] === md5($_POST['password']) && $login_user['email_confirmed'] == 0) {
            $error_message = "please confirm your email";
        } else {
            $error_message = "wrong password";
        }
    }
}

if (isset($_GET) && isset($_GET['email']) && isset($_GET['key'])) {
    $select_confirmation_key = "SELECT * FROM confirmation_key WHERE email ='";
    $select_confirmation_key.= $_GET['email'];
    $select_confirmation_key.= "' AND confirmation_key = '";
    $select_confirmation_key.= $_GET['key'];
    $select_confirmation_key.= "' LIMIT 1";
    $select_confirmation_key_result = $mysqli->query($select_confirmation_key);


    if ($select_confirmation_key_result->num_rows != 0) {
        $user_id = $select_confirmation_key_result->fetch_array()['user_id'];
        $delete_confirmation_key = "DELETE FROM confirmation_key WHERE user_id =";
        $delete_confirmation_key.= $user_id;
        $delete_confirmation_key.= " LIMIT 1";
        $delete_confirmation_key_result = $mysqli->query($delete_confirmation_key);


        if ($delete_confirmation_key_result === TRUE) {
            $update_user = "UPDATE users SET email_confirmed = 1 WHERE user_id = ";
            $update_user.= $user_id;

            $update_user_result = $mysqli->query($update_user);
        }

        $error_message = $mysqli->error;
    }
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

    <body >
        <div class="login-container">
            <form class="login-form" method="post" action="login.php">
                <h1><?php echo $error_message ?></h1>
                <label>Email</label>
                <input type="email" name="email" class="form-control" required="" autofocus="">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required="">
                <div class="checkbox mb-3">
                    <label>
                        <input type="checkbox" value="remember-me"> Remember me
                    </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block" name="submit" >Sign in</button>
                <a href="signup">Sign Up</button>
                <p class="mt-5 mb-3 text-muted">© 2017-2018</p>
            </form>
        </div>
    </body>
</html>