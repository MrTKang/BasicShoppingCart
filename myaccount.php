 <?php
    require("includes/functions.php");
    session_start();
    check_login_redirect($_SESSION, "myaccount.php");
?>


<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>My Account</title>
        <link href="css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <div class="account-container">
            <?php 
            if (isset($_SESSION['user'])) {
                display_my_checkouts($mysqli, $_SESSION['user']);
            }
            ?>
        </div>
        <a href="myproducts">my products</a>
        <a href="mycategories">my categories</a>
        <a href="myusers">my users</a>
    </body>
</html>