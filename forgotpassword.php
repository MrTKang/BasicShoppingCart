<?php
require("includes/credentials.php");
require("includes/functions.php");
session_start();

if (isset($_POST['reset_password']) && isset($_POST['email'])) {
    send_password_reset_email($mysqli, $_POST['email'], $GMAIL_ACCOUNT, $GMAIL_PASSWORD);
}

?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Forgot Password</title>
        <link href="css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <div class="send-password-reset-email-container">
                <?php display_send_password_reset_email_form() ?>
            </form>
        </div>
    </body>
</html>
