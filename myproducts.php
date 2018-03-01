<?php
require("includes/functions.php");
session_start();
create_product($mysqli);


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
    </head>
    <body>
        <div class="product-container">
            <?php display_my_products($mysqli) ?>
            <form class="product-form" id="productform" action="myproducts.php" method="post" enctype="multipart/form-data">
                Product name: <input class="form-control" type="text" name="name"><br>
                Description: <input class="form-control" type="text" name="description"><br>
                Price: <input class="form-control" type="number" name="price"><br>
                Image : <input class="form-control-file" type="file" name="upload" id="upload"><br>

            <select class="category-select form-control" name="category" form="productform">
                <?php display_category_form($mysqli) ?>
            </select>
                <button class="btn btn-lg btn-primary btn-block product-btn" type="submit" name="product">Submit</button>
            </form>

        </div>
    </body>
</html>