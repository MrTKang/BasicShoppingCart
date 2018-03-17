<?php
require("includes/functions.php");
session_start();

if (has_permissions($_SESSION['user']['permissions'], array(1024)) &&
 isset($_GET['edit_user']) && 
 isset($_GET['active'])) {
    set_user_activity($mysqli, $_GET['edit_user'], $_GET['active']);
}
?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>My Users</title>
        <link href="css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <div class="user-container">
            <?php display_user_list($mysqli) ?>
        </div>
    </body>
</html>