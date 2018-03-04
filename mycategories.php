<?php
require("includes/functions.php");
session_start();
create_category($mysqli);

if (isset($_GET['edit_category']) && isset($_GET['active'])) {
    set_category_activity($mysqli, $_GET['edit_category'], $_GET['active']);
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
            <?php display_category_list($mysqli) ?>
            <form class="category-form" action="mycategories.php" method="post">
                <h1 class="h3 mb-3 font-weight-normal">Add new category</h1>
                Category name: <input class="form-control" type="text" name="name"><br>
                <button class="btn btn-lg btn-primary btn-block category-btn" type="submit" name="category">submit</button>
            </form>
        </div>
    </body>
</html>