<?php
session_start();
require("includes/connection.php");
if (isset($_SESSION['user']) && isset($_POST['category'])) {
    $insert_category = "INSERT INTO categories (name) VALUES ('";
    $insert_category.= $_POST['name'];
    $insert_category.= "')";

    $insert_category_result = $mysqli->query($insert_category);
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
        <div class="category-container">
            <form class="category-form" action="mycategories.php" method="post">
                Category name: <input class="form-control" type="text" name="name"><br>
                <button class="btn btn-lg btn-primary btn-block category-btn" type="submit" name="category">submit</button>
            </form>
        </div>
    </body>
</html>