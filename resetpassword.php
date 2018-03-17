<?php
require("includes/credentials.php");
require("includes/functions.php");
session_start();

if (isset($_GET['email']) && isset($_GET['key']) && isset($_POST['password'])) {
    reset_password($mysqli, $_GET['email'], $_GET['key'], $_POST['password']);
}

?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Reset Password</title>
        <link href="css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <div class="reset-password-container">
                <?php display_password_reset_form($mysqli, $_GET['email'], $_GET['key']) ?>
            </form>
        </div>
    </body>
</html>
