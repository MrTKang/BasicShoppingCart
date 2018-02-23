<?php
    require("includes/connection.php");
    session_start();

    if (isset($_SESSION['user'])) {
        $select_user_checkouts = "SELECT user_checkout.checkout_id, checkouts.created_at FROM user_checkout ";
        $select_user_checkouts.= "INNER JOIN checkouts ";
        $select_user_checkouts.= "ON checkouts.checkout_id = user_checkout.checkout_id ";
        $select_user_checkouts.= "WHERE user_checkout.user_id=";
        $select_user_checkouts.= $_SESSION['user']['user_id'];

        $select_user_checkouts_result = $mysqli->query($select_user_checkouts);
    }

?>


<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../../../favicon.ico">
        <title>Signin Template for Bootstrap</title>
        <link href="css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <div class="account-container">
            <?php
            while ($checkout = $select_user_checkouts_result->fetch_array()){
                $select_products = "SELECT * FROM products ";
                $select_products.= "INNER JOIN product_checkout ";
                $select_products.= "ON products.product_id=product_checkout.product_id ";
                $select_products.= "WHERE product_checkout.checkout_id=";
                $select_products.= $checkout['checkout_id'];

                $select_products_result = $mysqli->query($select_products);
            ?>
            <div class="card">
                <div class="card-header">
                <?php echo $checkout['created_at'] ?>
                </div>

            <div class="card-body">
                <blockquote class="blockquote mb-0">
                    <?php
                    $total_price = 0;
                while ($product = $select_products_result->fetch_array()) {
                    $product_price = number_format($product['price'] * $product['quantity'], 2, '.', '');
                    $total_price+= $product_price;
                    ?>
                    <p> <?php echo $product['name'] ?> x <?php echo $product['quantity'] ?> = $<?php echo $product_price ?></p>
                    <?php
                }
                ?>
                <p>$<?php echo number_format($total_price, 2, '.', '') ?></p>
                </blockquote>
            </div>
            <?php
            }
            ?>
            </div>
        </div>
        <a href="myproducts">my products</a>
        <a href="mycategories">my categories</a>
    </body>
</html>