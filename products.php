<?php 
if (isset($_GET['action']) && $_GET['action'] == 'add') { 
	$_SESSION = add_to_cart($mysqli, $_GET['product_id'], $_SESSION); 
}

?>

<div class="products-container">

	<?php 
	if (!isset($_GET['category'])) {
		display_products($mysqli);
	} else {
		display_products_from_category($mysqli, $_GET['category']);
	}
	?>
</div>