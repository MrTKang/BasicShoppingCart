<?php
require("includes/connection.php");
session_start();

if (isset($_SESSION['user'])) {
    $select_user_product = "SELECT * FROM user_product ";
    $select_user_product.= "INNER JOIN products ";
    $select_user_product.= "ON user_product.product_id = products.product_id ";
    $select_user_product.= "WHERE user_product.user_id = ";
    $select_user_product.= $_SESSION['user']['user_id'];

    $select_user_product_result = $mysqli->query($select_user_product);
}

if (isset($_SESSION['user']) && isset($_POST['product'])) {
    $target_dir = "images/";
    $target_file = $target_dir.basename($_FILES["upload"]["name"]);

    $insert_product = "INSERT INTO product (name, description, price, image) VALUES ('";
    $insert_product.= $_POST['name'];
    $insert_product.= "', '";
    $insert_product.= $_POST['description'];
    $insert_product.= "', ";
    $insert_product.= $_POST['price'];
    $insert_product.= ", '";
    $insert_product.= $target_file;
    $insert_product.= "')";

    $insert_product_result = $mysqli->query($insert_product);
    $product_id = $mysqli->insert_id;

    $insert_user_product = "INSERT INTO user_product (user_id, product_id) VALUES (";
    $insert_user_product.= $_SESSION['user_id'];
    $insert_user_product.= ",";
    $insert_user_product.= $product_id;
    $insert_user_product.= ")";

    $insert_user_product_result = $mysqli->query($insert_user_product);

    $select_category_id = "SELECT category_id FROM categories WHERE name = '";
    $select_category_id.= $_POST['category'];
    $select_category_id.= "'";

    $select_category_id_result = $mysqli->query($select_category_id);

    $category = $select_category_id_result->fetch_array();
    $category_id = $category['category_id'];

    $insert_category_product = "INSERT INTO category_product(category_id, product_id) VALUES (";
    $insert_category_product.= $category_id;
    $insert_category_product.= ", ";
    $insert_category_product.= $product_id;
    $insert_category_product.= ")";

    $insert_category_product_result = $mysqli->query($insert_category_product);

    $upload_check = 1;
    $check = getimagesize($_FILES["upload"]["tmp_name"]);

    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $upload_check = 1;
    } else {
        echo "File is not an image.";
        $upload_check = 0;
    }

    if (move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["upload"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    } 
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
    </head>
    <body>
        <div class="products_container">
            <?php 
            while ($product = $select_user_product_result->fetch_array()) {
            ?>
            <p><?php echo $product['name'] ?></p>
            <?php
            } 
            ?>
            <form id="productform" action="myproducts.php" method="post" enctype="multipart/form-data">
                Product name: <input type="text" name="name"><br>
                Description: <input type="text" name="description"><br>
                Price: <input type="number" name="price"><br>
                Image : <input type="file" name="upload" id="upload"><br>
                <button type="submit" name="product">submit</button>
            </form>

            <select name="category" form="productform">
            <?php
            $select_categories = "SELECT * FROM categories";
            $select_categories_result = $mysqli->query($select_categories);
            while ($category = $select_categories_result->fetch_array()){
            ?>
                <option value="<?php echo $category['name'] ?>"><?php echo $category['name'] ?></option>
            <?php
            }
            ?>
            </select>
        </div>
    </body>
</html>