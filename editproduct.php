<?php
require("includes/credentials.php");
require("includes/functions.php");
session_start();

if (can_edit_product($mysqli, $_SESSION['user'], $_GET['product_id'])
    && isset($_POST['edit']) && isset($_GET['product_id'])) {
	edit_product($mysqli, $_GET['product_id'], $_POST);
}
?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Edit Product</title>
        <link href="css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <div class="edit-product-container">
                <?php display_edit_product_form($mysqli, $_GET['product_id']) ?>
            </form>
        </div>
    </body>
</html>