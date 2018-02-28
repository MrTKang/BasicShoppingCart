<?php

require("includes/connection.php");

//PRODUCTS
function display_products($mysqli) {
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
		$product_div = '
		<div class="product-card">
			<div class="product-image-container">
				<img class="product-image" alt="Thumbnail" src="{IMAGE}">
			</div>
			<div>{NAME}</div>
			<div>${PRICE}</div>
			<div><a href="index.php?page=products&action=add&product_id={PRODUCT_ID}">add to cart</a></div>
		</div>';

		$search = array("{IMAGE}", "{NAME}", "{PRICE}", "{PRODUCT_ID}");
		$replace = array($product['image'], $product['name'], $product['price'], $product['product_id']);

		$product_div = str_replace($search, $replace, $product_div);
		echo($product_div);
	}  
}

function add_to_cart($mysqli) {
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

}

//INDEX
function edit_cart() {
	if (isset($_POST['edit_cart'])) {
		foreach($_POST['quantity'] as $key => $value) {
			if ($value == 0) {
				unset($_SESSION['cart'][$key]);
			} else {
				$_SESSION['cart'][$key]['quantity'] = $value;
			}
		}
	}
}

function set_login() {
	$logged_in = isset($_SESSION['user']);
}

function display_categories($mysqli) {
	$select_categories = "SELECT * FROM categories";
	$categories_result = $mysqli->query($select_categories);

	while ($category = $categories_result->fetch_array()) {
		$category_anchor = '<a class="p-2 text-muted" href="index.php?category={CATEGORY}">{CATEGORY}</a>';
		$category_anchor = str_replace("{CATEGORY}", $category['name'], $category_anchor);
		echo($category_anchor);
	}
}

function display_cart($mysqli) {

	if (isset($_SESSION['cart'])) {
		$select_products = "SELECT * FROM products WHERE product_id IN (";
		foreach ($_SESSION['cart'] as $id => $value){
			$select_products.=$id;
			$select_products.=", ";
		}

		$select_products = substr($select_products, 0, -2);
		$select_products.= ") ORDER BY name ASC";
		$products_result = $mysqli->query($select_products);
		if ($products_result->num_rows == 0) {
			$cart_empty = true;
		} else {
			$checkout_price = 0;
			while ($product = $products_result->fetch_array()){
				$quantity = $_SESSION['cart'][$product['product_id']]['quantity'];
				$checkout_price += $product['price'] * $quantity;

				$cart_item = '<p>{PRODUCT_NAME} X <input type="text" name="quantity[{PRODUCT_ID}]" value="{QUANTITY}" size="5"/> = ${PRICE}</p>';
				$search = array("{PRODUCT_NAME}", "{PRODUCT_ID}", "{QUANTITY}", "{PRICE}");
				$replace = array($product['name'], $product['product_id'], $quantity, number_format($product['price'] * $quantity , 2, '.', ''));
				$cart_item = str_replace($search, $replace, $cart_item);
				echo($cart_item);
			}
		}
	} else {
		$cart_empty = true;
	}
	if (isset($cart_empty) && $cart_empty) {
		echo('<p>Your Cart is empty. Please add some products.</p>');
	}	
}

?>