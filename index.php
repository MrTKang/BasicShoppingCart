<?php
require("includes/functions.php");
session_start();

if (isset($_POST['edit_cart']) && isset($_SESSION['user'])) {
	edit_user_cart($mysqli, $_SESSION['user'], $_POST['quantity']);
} else if (isset($_POST['edit_cart']) && isset($_SESSION['cart'])) {
	edit_session_cart($_SESSION['cart'], $_POST['quantity']);
}

$logged_in = is_logged_in();
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
  
<html> 
	<head> 
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
		<link rel="stylesheet" href="css/style.css" /> 
		<link rel="stylesheet" href="css/bootstrap/dist/css/bootstrap.min.css" /> 
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
		<script src="css/bootstrap/dist/js/bootstrap.min.js"></script>
		<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
		<title>Shopping Cart</title> 
	</head> 
	<body> 
		<div class="container"> 
			<header class="site-header">
				<div class="header-container">
					<a class="site-logo" href="index.php">FreeStore</a>
					<nav class="site-nav"> 
						<ul class="main-nav">
							<li><a href="">Shop</a></li>
							<li><a href="">About</a></li>
							<li><a href="">Contact</a></li>
						</ul>
						<ul class="secondary-nav">
							<?php if ($logged_in) { ?>
							<li><a href="myaccount.php">My Account</a></li>
							<li><a href="login.php">Logout</a></li>
							<?php  } else { ?>
							<li><a href="login.php">Login</a></li>
							<?php } ?>
							<li><a href="checkout.php">Cart</a></li>
						</nav>
					</nav>
				</div>
			</header>

			<nav class="category-nav">
				<div class="main-category-nav-container">
					<ul class="main-category-nav">
						<?php 
						if (isset($_GET['category'])) {
							display_categories($mysqli, $_GET['category']); 
						} else {
							display_categories($mysqli, '');
						}
						?>
					</ul>
				</div>
			</nav>

		<!--	<div class="nav-scroller py-1 mb-2">
				<nav class="nav d-flex justify-content-between">
					<?php 
					if (isset($_GET['category'])) {
						display_categories($mysqli, $_GET['category']); 
					} else {
						display_categories($mysqli, ""); 
					}
					?>
				</nav>
			</div>
			<div class="jumbotron p-3 p-md-5 text-white rounded bg-dark">
				<div class="col-md-6 px-0">
					<p class="lead my-3">Welcome to Kevin's Store. This is a basic online store created in php and html. You can view the source code at <a href="https://github.com/supakang/BasicShoppingCart">my github</a>.</p>
				</div>
			</div>
		-->
      		<?php require("products.php") ?>
		</div>


		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	    	<div class="modal-dialog" role="document">
	    		<form method="post" action="index.php">
	    			<div class="modal-content">
	    				<div class="modal-header">
	    					<h5 class="modal-title" id="exampleModalLabel">CART</h5>
			            	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			                	<span aria-hidden="true">&times;</span>
			              	</button>
	            		</div>
						<div class="modal-body">
							<?php 
								if (isset($_SESSION['user'])) {
									display_user_cart($mysqli, $_SESSION['user']);
								} else if (isset($_SESSION['cart'])) {
									display_session_cart($mysqli, $_SESSION['cart']);
								} else {
									display_empty_cart();
								}
							?>
	            		</div>
	            		<div class="modal-footer">
	            			<button type="submit" name="edit_cart" class="btn btn-primary">Save changes</button>
	            			<a href="checkout.php" class="btn btn-primary">Checkout</a>
	            		</div>
	          		</div>
	        	</form>
	      	</div>
	    </div>
    </body> 
</html>