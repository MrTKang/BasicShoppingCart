<?php
require("includes/credentials.php");
require("includes/functions.php");
session_start();

if (isset($_POST['edit']) && isset($_GET['category_id'])) {
	edit_category($mysqli, $_GET['category_id'], $_POST);
}
?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Edit Product</title>
        <link href="css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <div class="edit-category-container">
                <?php display_edit_category_form($mysqli, $_GET['category_id']) ?>
            </form>
        </div>
    </body>
</html>