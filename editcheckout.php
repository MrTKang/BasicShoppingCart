<?php
require("includes/credentials.php");
require("includes/functions.php");
session_start();

if (isset($_GET['checkout_id'])) {
    $url = "editcheckout.php?checkout_id=";
    $url.= $_GET['checkout_id'];
    check_login_redirect($_SESSION, $url);
}

if (has_permissions($_SESSION['user']['permissions'], array(1024)) &&
 isset($_POST['edit_checkout']) && 
 isset($_GET['checkout_id'])) {
    print_r($_POST);
    edit_checkout($mysqli, $_GET['checkout_id'], $_POST);
}
?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Edit Checkout</title>
        <link href="css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <div class="edit-checkout-container">
                <?php display_edit_checkout_form($mysqli, $_GET['checkout_id']) ?>
            </form>
        </div>
    </body>
</html>