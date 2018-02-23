<?php
require("includes/connection.php");
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

            <?php 
                if (isset($_SESSION['cart'])) {
                    $select_products = "SELECT * FROM products WHERE product_id IN (";
                    foreach ($_SESSION['cart'] as $product_id => $quantity) {
                        $select_products.= $product_id;
                        $select_products.= ", ";
                    }
                    $select_products = substr($select_products, 0, -2);
                    $select_products.= ")";
                    $select_products_result = $mysqli->query($select_products);
                    $total_price = 0;
                    while ($product = $select_products_result->fetch_array()) {
                        $quantity = $_SESSION['cart'][$product['product_id']]['quantity'];
                        $total_price+= $product['price'] * $quantity;
            ?>

                    <p><?php echo $product['name'] ?> X <?php echo $quantity ?> = $<?php echo(number_format($product['price'] * $quantity , 2, '.', ''))?></p>
            <?php
                    }
            ?>
            <p>Total Price: $<?php echo(number_format($total_price, 2, '.', '')) ?> </p>
            <?php 
                }
            ?>

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