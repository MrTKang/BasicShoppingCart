<?php
require("includes/credentials.php");
require("includes/functions.php");
session_start();

$status = sign_up_user($mysqli, $GMAIL_ACCOUNT, $GMAIL_PASSWORD);
resend_email($mysqli, $GMAIL_ACCOUNT, $GMAIL_PASSWORD);
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
                <?php display_sign_up_form($status) ?>
                <p class="mt-5 mb-3 text-muted">© 2017-2018</p>
            </form>
        </div>
    </body>
</html>