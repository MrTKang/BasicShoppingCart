<?php
require("includes/functions.php");
session_start();

if (isset($_POST['add_to_cart'])) {
    $_SESSION['cart'] = add_quantity_to_cart($_SESSION['cart'], $_GET['product_id'], $_POST['quantity']);
}

?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Signin Template for Bootstrap</title>
        <link href="css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <script src="css/bootstrap/dist/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="product-container">
            <?php 
            if (isset($_GET['product_id'])) {
                display_product_details($mysqli, $_GET['product_id'], $_SESSION['cart']); 
            }
            ?>
        </div>
    </body>
</html>