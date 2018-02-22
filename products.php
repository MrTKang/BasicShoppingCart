<div class="products-container">
	<?php
	if (!isset($_GET['category'])) {
		$select_products = "SELECT * FROM products ORDER BY name ASC";
	} else {
		$select_products = "SELECT products.name, products.price, products.image, products.product_id FROM products ";
		$select_products.= "INNER JOIN category_product ";
		$select_products.= "ON category_product.product_id = products.product_id ";
		$select_products.= "INNER JOIN categories ";
		$select_products.= "ON categories.category_id = category_product.category_id WHERE categories.name = '";
		$select_products.= $_GET['category'];
		$select_products.= "' ORDER BY name ASC";
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