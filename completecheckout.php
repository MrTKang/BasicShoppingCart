<?php
require("includes/functions.php");
session_start();

if (isset($_GET['tx'])) {
    $key_array = handle_transaction_id($_GET['tx'], $mysqli, $_SESSION['user']);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 

<html xmlns="http://www.w3.org/1999/xhtml"> 
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
        <main role="main" class="container">
            <div class="checkout-message">

                <?php 
                if (isset($key_array)) {
                    display_complete_checkout_message($key_array);
                } else {
                    echo('<h1>Checkout Unsuccessful</h1>');
                }
                ?>
            </div>
        </main>
    <body>
</html>