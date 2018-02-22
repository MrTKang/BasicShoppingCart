<?php
require("includes/connection.php");
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
						<a class="text-muted" href="#">Subscribe</a> |
						<a class="text-muted" href="myaccount">My Account</a>
					</div>
					<div class="col-4 text-center">
						<a class="blog-header-logo text-dark" href="#">Kevin's Store</a>
					</div>
					<div class="col-4 d-flex justify-content-end align-items-center">
						<a class="text-muted" href="#">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-3"><circle cx="10.5" cy="10.5" r="7.5"></circle><line x1="21" y1="21" x2="15.8" y2="15.8"></line></svg>
						</a>
						<a class="btn btn-sm btn-outline-secondary"  href="#" data-toggle="modal" data-target="#exampleModal">Cart</a>
						<a class="btn btn-sm btn-outline-secondary" href="login">Log in</a>
					</div>
				</div>
			</header>
			<div class="nav-scroller py-1 mb-2">
				<nav class="nav d-flex justify-content-between">
					<a class="p-2 text-muted" href="#">HOME</a>
					<?php 
					$select_categories = "SELECT * FROM categories";
					$categories_result = $mysqli->query($select_categories);

					while ($category = $categories_result->fetch_array()) {
					?>
					<a class="p-2 text-muted" href="index.php?category=<?php echo $category['name'] ?>"><?php echo $category['name'] ?> </a>
					<?php 
					}
					?>
				</nav>
			</div>
			<div class="jumbotron p-3 p-md-5 text-white rounded bg-dark">
				<div class="col-md-6 px-0">
					<p class="lead my-3">Welcome to Kevin's Store. This is a basic online store created in php and html. You can view the source code at my github.</p>
				</div>
			</div>
      		<?php require("products.php") ?>
		</div>
    </body> 
</html>