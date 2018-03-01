<?php
require("includes/credentials.php");
require("includes/functions.php");
session_start();
$messages = sign_up_user($mysqli, $GMAIL_ACCOUNT, $GMAIL_PASSWORD);
?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Sign Up</title>
        <link href="css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <div class="signup-container">
            <form class="signup-form" method="post" action="signup.php">
                <h1 class="h3 mb-3 font-weight-normal">Please sign up</h1>
                <h6> <?php echo $messages['error'] ?></h6>
                <h6> <?php echo $messages['message'] ?></h6>
                <label for="name">Your Name</label>
                <input type="text" class="form-control" name="name" required="" autofocus="">
                <label for="email">Email address</label>
                <input type="email" class="form-control" name="email" required="" autofocus="">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" required="">
                <label for="passwordagain">Password Again</label>
                <input type="password" class="form-control" name="passwordagain" required="">
                <button id="sign-up-btn" class="btn btn-lg btn-primary btn-block"  name="submit" type="submit">Sign up</button>
                <p class="mt-5 mb-3 text-muted">© 2017-2018</p>
            </form>
        </div>
    </body>
</html>