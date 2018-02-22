<?php
if (isset($_GET['action']) && $_GET['action'] == 'add') {
	$product_id = intval($_GET['product_id']);
	if (isset($_SESSION['cart'][$product_id])) {
		$_SESSION['cart'][$product_id]['quantity']++;
	} else {
		$select_product = "SELECT * FROM products WHERE product_id = ";
		$select_product.= $product_id;

		$select_product_result = $mysqli->query($select_product);
		if ($select_product_result->num_rows != 0) {
			$product = $select_product_result->fetch_array();
			$_SESSION['cart'][$product['product_id']] = array("quantity" => 1, "price" => $product['price']);
		}
	}
}
?>



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

	$select_products_result = $mysqli->query($select_products);
	while ($product = $select_products_result->fetch_array()) {
	?>
	<div class="product-card">
		<div class="product-image-container">
			<img class="product-image" alt="Thumbnail" src="<?php echo $product['image'] ?>">
		</div>
		<div><?php echo $product['name'] ?></div>
		<div>$<?php echo $product['price'] ?></div>
		<div><a href="index.php?page=products&action=add&product_id=<?php echo $product['product_id'] ?>">add to cart</a></div>
	</div>
	<?php 
	}  
	?>
</div>