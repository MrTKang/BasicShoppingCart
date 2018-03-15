<?php
require("includes/credentials.php");
require("includes/functions.php");
session_start();

if (has_permissions($_SESSION['user']['permissions'], array(1024)) 
    && isset($_POST['edit']) && isset($_GET['user_id'])) {
    print_r($_POST);
    edit_user($mysqli, $_GET['user_id'], $_POST);
}
?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Edit User</title>
        <link href="css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <div class="edit-user-container">
                <?php display_edit_user_form($mysqli, $_GET['user_id']) ?>
            </form>
        </div>
    </body>
</html>
