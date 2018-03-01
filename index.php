<?php
require("includes/functions.php");
session_start();
edit_cart();
$logged_in = login();
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
		<title>Shopping Cart</title> 
	</head> 
	<body> 
		<div class="container"> 
			<header class="blog-header py-3">
				<div class="row flex-nowrap justify-content-between align-items-center">
					<div class="col-4 pt-1">
						<a class="text-muted" href="#">Subscribe</a>
						<?php 
						if ($logged_in) {
						?>
						|
						<a class="text-muted" href="myaccount">My Account</a>
						<?php
						}
						?>
					</div>
					<div class="col-4 text-center">
						<a class="blog-header-logo text-dark" href="#">Kevin's Store</a>
					</div>
					<div class="col-4 d-flex justify-content-end align-items-center">
						<a class="text-muted" href="#">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-3"><circle cx="10.5" cy="10.5" r="7.5"></circle><line x1="21" y1="21" x2="15.8" y2="15.8"></line></svg>
						</a>
						<a class="btn btn-sm btn-outline-secondary"  href="#" data-toggle="modal" data-target="#exampleModal">Cart</a>

						<?php 
						if ($logged_in) {
						?>
						<a class="btn btn-sm btn-outline-secondary" href="login">Log out</a>
						<?php
						} else {
						?>
						<a class="btn btn-sm btn-outline-secondary" href="login">Log in</a>
						<?php
						}
						?>
					</div>
				</div>
			</header>
			<div class="nav-scroller py-1 mb-2">
				<nav class="nav d-flex justify-content-between">
					<a class="p-2 text-muted" href="#">HOME</a>
					<?php display_categories($mysqli) ?>
				</nav>
			</div>
			<div class="jumbotron p-3 p-md-5 text-white rounded bg-dark">
				<div class="col-md-6 px-0">
					<p class="lead my-3">Welcome to Kevin's Store. This is a basic online store created in php and html. You can view the source code at my github.</p>
				</div>
			</div>
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
							<?php display_cart($mysqli) ?>
	            		</div>
	            		<div class="modal-footer">
	            			<button type="submit" name="edit_cart" class="btn btn-primary">Save changes</button>
	            			<a href="checkout" class="btn btn-primary">Checkout</a>
	            		</div>
	          		</div>
	        	</form>
	      	</div>
	    </div>
    </body> 
</html>