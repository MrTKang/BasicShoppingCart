<?php
require("includes/functions.php");
session_start();

if (isset($_POST['checkout']) && isset($_SESSION['user'])) {
    $total_amount = complete_checkout($mysqli, $_POST['address'], $_POST['postalcode'], $_SESSION['user']);
}

?>

<html lang="en"> 
    <head> 
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
        <link rel="stylesheet" href="css/reset.css" /> 
        <link rel="stylesheet" href="css/style.css" /> 
        <link rel="stylesheet" href="css/bootstrap/dist/css/bootstrap.min.css" /> 
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <script src="css/bootstrap/dist/js/bootstrap.min.js"></script>
        <title>Checkout</title> 
    </head> 

    <body> 
        <div class="make-payment-container">
            <?php 
            if (isset($total_amount)) {
                display_complete_payment_form($total_amount);
            }
            ?>
        </div>
    <body>
</html>