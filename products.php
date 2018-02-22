<div class="products-container">
	<?php
	if (!isset($_GET['category'])) {
		$select_products = "SELECT * FROM products ORDER BY name ASC";
	}
	$select_products_results = $mysqli->query($select_products);
	while ($product = $select_products_results->fetch_array()) {
	?>
	<div class="product-card">
		<div class="product-image-container">
			<img class="product-image" alt="Thumbnail" src="<?php echo $product['image'] ?>">
		</div>
		<div><?php echo $product['name'] ?></div>
		<div>$<?php echo $product['price'] ?></div>
		<div><a href="">add to cart</a></div>
	</div>
	<?php 
	}  
	?>
</div>