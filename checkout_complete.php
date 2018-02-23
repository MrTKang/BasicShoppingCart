<?php
require("includes/connection.php");
session_start();

if (isset($_POST['checkout']) && isset($_SESSION['user'])) {

    $insert_checkout = "INSERT INTO checkouts (address, postal_code) VALUES ('";
    $insert_checkout.= $_POST['address'];
    $insert_checkout.= "', '";
    $insert_checkout.= $_POST['postalcode'];
    $insert_checkout.= "')";
    $insert_checkout_result = $mysqli->query($insert_checkout);

    $checkout_id = $mysqli->insert_id;

    $message = "";

    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $insert_product_checkout = "INSERT INTO product_checkout (product_id, checkout_id, quantity) VALUES (";
        $insert_product_checkout.= $product_id;
        $insert_product_checkout.= ",";
        $insert_product_checkout.= $checkout_id;
        $insert_product_checkout.= ",";
        $insert_product_checkout.= $quantity['quantity'];
        $insert_product_checkout.= ")";

        $insert_product_checkout_result = $mysqli->query($insert_product_checkout);

        $message.= $insert_product_checkout;
    }

    $insert_user_checkout = "INSERT INTO user_checkout (user_id, checkout_id) VALUES (";
    $insert_user_checkout.= $_SESSION['user']['user_id'];
    $insert_user_checkout.= ", ";
    $insert_user_checkout.= $checkout_id;
    $insert_user_checkout.= ")";

    $insert_user_checkout_result = $mysqli->query($insert_user_checkout);

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
                <h1>Checkout Successful! <?php echo $message ?></h1>
                <p class="lead">Thanks for using our dummy shop you bought nothing</p>
            </div>
        </main>
    <body>
</html>