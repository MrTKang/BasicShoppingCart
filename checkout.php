<?php
require("includes/functions.php");
session_start();
?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Login</title>
        <link href="css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body >
        <div class="checkout-container">
            <form class="checkout-form" method="post" action="checkout_complete.php">
                <?php display_checkout_cart($mysqli) ?>

                <label>Address</label>
                <input type="text" name="address" class="form-control" required="" autofocus="">
                <label>Postal Code</label>
                <input type="text" name="postalcode" class="form-control" required="" autofocus="">
                <button class="checkout-btn btn btn-lg btn-primary btn-block" name="checkout" >Checkout</button>
                <p class="mt-5 mb-3 text-muted">© 2017-2018</p>
            </form>
        </div>
    </body>
</html>