<?php

function select_products_by_category($mysqli, $category) {
	$select_products = "SELECT products.name, products.price, products.image, products.product_id FROM products ";
	$select_products.= "INNER JOIN category_product ";
	$select_products.= "ON category_product.product_id = products.product_id ";
	$select_products.= "INNER JOIN categories ";
	$select_products.= "ON categories.category_id = category_product.category_id WHERE categories.name = '";
	$select_products.= $category;
	$select_products.= "' and products.available = 1 ORDER BY name ASC";

	$select_products_result = $mysqli->query($select_products);
	return $select_products_result;
}


function select_product($mysqli, $product_id) {
	$select_product = "SELECT * FROM products WHERE product_id = ";
	$select_product.= $product_id;
    $select_product.= " LIMIT 1";

	return $mysqli->query($select_product);
}

function select_cart_product_by_user_product($mysqli, $user_id, $product_id) {
	$select_cart_products = "SELECT * FROM cart_product WHERE user_id = ";
	$select_cart_products.= $user_id;
	$select_cart_products.= " AND product_id = ";
	$select_cart_products.= $product_id;
	$select_cart_products.= " LIMIT 1";

	$select_cart_products_result = $mysqli->query($select_cart_products);
	return $select_cart_products_result;
}

function select_cart_products($mysqli, $user_id, $product_quantities) {
	$select_cart_products = "SELECT * FROM cart_product WHERE user_id = ";
	$select_cart_products.= $user_id;
	$select_cart_products.= " AND product_id in (";

	foreach ($product_quantities as $key => $value) {
		$select_cart_products.= $key;
		$select_cart_products.= ", ";
	}
	$select_cart_products = substr($select_cart_products, 0, -2);
	$select_cart_products.= ")";

	return $mysqli->query($select_cart_products);
}

function delete_cart_product($mysqli, $user_id, $product_id) {
	$delete_cart_product = "DELETE FROM cart_product WHERE user_id = ";
	$delete_cart_product.= $user_id;
	$delete_cart_product.= " AND product_id = ";
	$delete_cart_product.= $product_id;

	$delete_cart_product_result = $mysqli->query($delete_cart_product);
}


function select_cart_product($mysqli, $user_id) {
	$select_cart_products = "SELECT * FROM cart_product 
							INNER JOIN products
							ON cart_product.product_id = products.product_id 
							WHERE user_id = ";
	$select_cart_products.= $user_id;

	return $mysqli->query($select_cart_products);
}


function select_products_from_cart($mysqli, $cart) {
	$select_products = "SELECT * FROM products WHERE product_id IN (";
	foreach ($cart as $id => $value){
		$select_products.=$id;
		$select_products.=", ";
	}
	$select_products = substr($select_products, 0, -2);
	$select_products.= ") ORDER BY name ASC";
	return $mysqli->query($select_products);
}

function select_user_by_email($mysqli, $email) {
	$select_user = "SELECT * FROM users WHERE email='";
	$select_user.= $email;
	$select_user.= "'";

	return $mysqli->query($select_user);
}

function insert_user($mysqli, $name, $email, $password) {
    $insert_user = "INSERT INTO users (name, email, password) VALUES ('";
    $insert_user.= $name;
    $insert_user.= "', '";
    $insert_user.= $email;
    $insert_user.= "', MD5('";
    $insert_user.= $password;
    $insert_user.= "'))";

    return $mysqli->query($insert_user);
}

function insert_confirmation_key($mysqli, $user_id, $confirmation_key, $email) {
    $insert_confirmation_key = "INSERT INTO confirmation_key (user_id, confirmation_key, email) VALUES (";
    $insert_confirmation_key.= $user_id;
    $insert_confirmation_key.= ", '";
    $insert_confirmation_key.= $confirmation_key;
    $insert_confirmation_key.= "', '";
    $insert_confirmation_key.= $email;
    $insert_confirmation_key.= "')";

    return $mysqli->query($insert_confirmation_key);
}

function select_confirmation_key_by_email($mysqli, $email) {
	$select_confirmation_key = "SELECT * FROM confirmation_key WHERE email='";
	$select_confirmation_key.= $email;
	$select_confirmation_key.= "' LIMIT 1";
	return $mysqli->query($select_confirmation_key);
}

function select_confirmation_key_by_email_and_key($mysqli, $email, $key) {
    $select_confirmation_key = "SELECT * FROM confirmation_key WHERE email ='";
    $select_confirmation_key.= $email;
    $select_confirmation_key.= "' AND confirmation_key = '";
    $select_confirmation_key.= $key;
    $select_confirmation_key.= "' LIMIT 1";
    $select_confirmation_key_result = $mysqli->query($select_confirmation_key);
	return $select_confirmation_key_result;
}



function delete_confirmation_key($mysqli, $user_id) {
    $delete_confirmation_key = "DELETE FROM confirmation_key WHERE user_id =";
    $delete_confirmation_key.= $user_id;
    $delete_confirmation_key.= " LIMIT 1";
    $delete_confirmation_key_result = $mysqli->query($delete_confirmation_key);
    return $delete_confirmation_key_result;
}

function update_user_email_confirmation($mysqli, $user_id, $email_confirmed) {
    $update_user = "UPDATE users SET email_confirmed = ";
    $update_user.= $email_confirmed;
    $update_user.= " WHERE user_id = ";
    $update_user.= $user_id;
	return $mysqli->query($update_user);
}

function select_checkouts_by_user($mysqli, $user_id) {
    $select_user_checkouts = "SELECT * FROM user_checkout ";
    $select_user_checkouts.= "INNER JOIN checkouts ";
    $select_user_checkouts.= "ON checkouts.checkout_id = user_checkout.checkout_id ";
    $select_user_checkouts.= "INNER JOIN shipment_status ";
    $select_user_checkouts.= "ON shipment_status.shipment_status_id = checkouts.shipment_status_id ";
    $select_user_checkouts.= "WHERE user_checkout.user_id=";
    $select_user_checkouts.= $user_id;

    $select_user_checkouts_result = $mysqli->query($select_user_checkouts);
    return $select_user_checkouts_result;
}

function select_products_by_checkout($mysqli, $checkout_id) {
    $select_products = "SELECT * FROM products ";
    $select_products.= "INNER JOIN product_checkout ";
    $select_products.= "ON products.product_id=product_checkout.product_id ";
    $select_products.= "WHERE product_checkout.checkout_id=";
    $select_products.= $checkout_id;

    $select_products_result = $mysqli->query($select_products);
    return $select_products_result;
}

function update_product_inventory($mysqli, $newinventory, $product_id) {
    $update_product_inventory = "UPDATE products SET inventory = ";
    $update_product_inventory.= $newinventory;
    $update_product_inventory.= " WHERE product_id = ";
    $update_product_inventory.= $product_id;
    
    $update_product_inventory_result = $mysqli->query($update_product_inventory);
    return $update_product_inventory_result;
}


function select_products_by_user($mysqli, $user_id) {
    $select_user_product = "SELECT * FROM user_product ";
    $select_user_product.= "INNER JOIN products ";
    $select_user_product.= "ON user_product.product_id = products.product_id ";
    $select_user_product.= "WHERE user_product.user_id = ";
    $select_user_product.= $_SESSION['user']['user_id'];

    $select_user_product_result = $mysqli->query($select_user_product);

	return $select_user_product_result;
}

function insert_product($mysqli, $name, $description, $price, $image) {
    $insert_product = "INSERT INTO products (name, description, price, image) VALUES ('";
    $insert_product.= $name;
    $insert_product.= "', '";
    $insert_product.= $description;
    $insert_product.= "', ";
    $insert_product.= $price;
    $insert_product.= ", '";
    $insert_product.= $image;
    $insert_product.= "')";

    $insert_product_result = $mysqli->query($insert_product);
    return $insert_product_result;

}

function insert_user_product($mysqli, $user_id, $product_id) {
    $insert_user_product = "INSERT INTO user_product (user_id, product_id) VALUES (";
    $insert_user_product.= $user_id;
    $insert_user_product.= ",";
    $insert_user_product.= $product_id;
    $insert_user_product.= ")";

    $insert_user_product_result = $mysqli->query($insert_user_product);
    return $insert_user_product_result;
}

function select_category_by_name($mysqli, $name) {
    $select_category_id = "SELECT category_id FROM categories WHERE name = '";
    $select_category_id.= $name;
    $select_category_id.= "'";

    $select_category_id_result = $mysqli->query($select_category_id);
    return $select_category_id_result;
}

function insert_category_product($mysqli, $category_id, $product_id) {
    $insert_category_product = "INSERT INTO category_product(category_id, product_id) VALUES (";
    $insert_category_product.= $category_id;
    $insert_category_product.= ", ";
    $insert_category_product.= $product_id;
    $insert_category_product.= ")";

    $insert_category_product_result = $mysqli->query($insert_category_product);

}

function update_product($mysqli, $product_id, $name, $price, $description) {
	$update_product = "UPDATE products SET name = '";
	$update_product.= $name;
	$update_product.= "', price = ";
	$update_product.= $price;
	$update_product.= ", description = '";
	$update_product.= $description;
	$update_product.= "' WHERE product_id = ";
	$update_product.= $product_id;

	$update_product_result = $mysqli->query($update_product);
	return $update_product_result;
}


function insert_category($mysqli, $name) {
    $insert_category = "INSERT INTO categories (name) VALUES ('";
    $insert_category.= $name;
    $insert_category.= "')";

    return $mysqli->query($insert_category);
}

function select_categories($mysqli) {
	$select_categories = "SELECT * FROM categories";
	return $mysqli->query($select_categories);
}


function select_category($mysqli, $category_id) {
    $select_category = "SELECT * FROM categories WHERE category_id = ";
    $select_category.= $category_id;
    $select_category.= " LIMIT 1";

    return $mysqli->query($select_category);
}

function update_category($mysqli, $category_id, $name) {
    $update_category = "UPDATE categories SET name ='";
    $update_category.= $name;
    $update_category.= "' WHERE category_id = ";
    $update_category.= $category_id;

    $update_category_result = $mysqli->query($update_category);
    return $update_category_result;

}

function select_products_by_cart_user($mysqli, $user_id) {
    $select_products = "SELECT * FROM products
                            INNER JOIN cart_product
                            ON products.product_id = cart_product.product_id 
                            WHERE cart_product.user_id = ";
    $select_products.= $user_id;

    $select_products_result = $mysqli->query($select_products);
    return $select_products_result;
}

function select_checkouts_by_user_and_payment_status($mysqli, $user_id, $payment_confirmed) {
    $select_checkout = "SELECT * FROM checkouts 
                        INNER JOIN user_checkout
                        ON user_checkout.checkout_id = checkouts.checkout_id 
                        WHERE user_checkout.user_id = ";
    $select_checkout.= $user_id;
    $select_checkout.= " AND checkouts.payment_confirmed = ";
    $select_checkout.= $payment_confirmed;

    $select_checkout_result = $mysqli->query($select_checkout);
    return $select_checkout_result;
}

function delete_checkouts_by_ids($mysqli, $checkout_ids){
    $delete_product_checkout = "DELETE FROM product_checkout WHERE checkout_id IN (";
    $delete_user_checkout = "DELETE FROM user_checkout WHERE checkout_id IN (";
    $delete_checkout = "DELETE FROM checkouts WHERE checkout_id IN (";
    foreach ($checkout_ids as $checkout_id) {
        $delete_product_checkout.= $checkout_id;
        $delete_product_checkout.= ", ";
        $delete_user_checkout.= $checkout_id;
        $delete_user_checkout.= ", ";
        $delete_checkout.= $checkout_id;
        $delete_checkout.= ", ";
    }
    $delete_product_checkout = substr($delete_product_checkout, 0, -2);
    $delete_product_checkout.= ")";
    $delete_user_checkout = substr($delete_user_checkout, 0, -2);
    $delete_user_checkout.= ")";
    $delete_checkout = substr($delete_checkout, 0, -2);
    $delete_checkout.= ")";

    $delete_product_checkout_result = $mysqli->query($delete_product_checkout);
    $delete_user_checkout_result = $mysqli->query($delete_user_checkout);
    $delete_checkout_result = $mysqli->query($delete_checkout);
}

function insert_checkout($mysqli, $address, $postalcode, $total_amount) {
    $insert_checkout = "INSERT INTO checkouts (address, postal_code, shipment_status_id, location, total_amount) VALUES ('";
    $insert_checkout.= $address;
    $insert_checkout.= "', '";
    $insert_checkout.= $postalcode;
    $insert_checkout.= "', 0, 'my store', ";
    $insert_checkout.= $total_amount;
    $insert_checkout.= ")";
    $insert_checkout_result = $mysqli->query($insert_checkout);
    return $insert_checkout_result;
}


function delete_unconfirmed_checkouts($mysqli, $user_id) {
    $select_checkout_result = select_checkouts_by_user_and_payment_status($mysqli, $user_id, 0);

    if ($select_checkout_result->num_rows > 0){
        $checkout_ids = array();
        while ($checkout = $select_checkout_result->fetch_array()) {
            array_push($checkout_ids, $checkout['checkout_id']);
        }
        delete_checkouts_by_ids($mysqli, $checkout_ids);
    }
}

function insert_product_checkout($mysqli, $product_id, $checkout_id, $quantity) {
    $insert_product_checkout = "INSERT INTO product_checkout (product_id, checkout_id, quantity) VALUES (";
    $insert_product_checkout.= $product_id;
    $insert_product_checkout.= ",";
    $insert_product_checkout.= $checkout_id;
    $insert_product_checkout.= ",";
    $insert_product_checkout.= $quantity;
    $insert_product_checkout.= ")";

    $insert_product_checkout_result = $mysqli->query($insert_product_checkout);

    return $insert_product_checkout_result;
}


function insert_user_checkout($mysqli, $user_id, $checkout_id) {
    $insert_user_checkout = "INSERT INTO user_checkout (user_id, checkout_id) VALUES (";
    $insert_user_checkout.= $user_id;
    $insert_user_checkout.= ", ";
    $insert_user_checkout.= $checkout_id;
    $insert_user_checkout.= ")";

    $insert_user_checkout_result = $mysqli->query($insert_user_checkout);

    return $insert_user_checkout_result;
}


function select_user_checkouts($mysqli, $user_id) {
    $select_user_checkouts = "SELECT * FROM user_checkout 
                                INNER JOIN checkouts 
                                ON user_checkout.checkout_id = checkouts.checkout_id 
                                INNER JOIN shipment_status
                                ON checkouts.shipment_status_id = shipment_status.shipment_status_id
                                WHERE user_checkout.user_id = ";
    $select_user_checkouts.= $user_id;

    $select_user_checkouts_result = $mysqli->query($select_user_checkouts);
    return $select_user_checkouts_result;
}

function update_user($mysqli, $user_id, $name, $email, $permissions) {
    $update_user = "UPDATE users SET name = '";
    $update_user.= $name;
    $update_user.= "', email = '";
    $update_user.= $email;
    $update_user.= "', permissions = ";

    $permission = 0;

    foreach ($permissions as $permission_bit) {
        $permission += $permission_bit;
    }

    $update_user.= $permission;
    $update_user.= " WHERE user_id = ";
    $update_user.= $user_id;

    $update_user_result = $mysqli->query($update_user);
    return $update_user_result;
}


function select_checkout($mysqli, $checkout_id) {
    $select_checkouts = "SELECT * FROM checkouts
                            INNER JOIN shipment_status 
                            ON checkouts.shipment_status_id = shipment_status.shipment_status_id
                            WHERE checkouts.checkout_id =";
    $select_checkouts.= $checkout_id;

    $select_checkouts_result = $mysqli->query($select_checkouts);

    return $select_checkouts_result;
}

function update_checkout($mysqli, $checkout_id, $ship_by , $address, $postalcode, $shipment_status_id, $location) {
    $update_checkout = "UPDATE checkouts SET ship_by = '";
    $update_checkout.= $ship_by;
    $update_checkout.= "', address = '";
    $update_checkout.= $address;
    $update_checkout.= "', postal_code = '";
    $update_checkout.= $postalcode;
    $update_checkout.= "', shipment_status_id = ";
    $update_checkout.= $shipment_status_id;
    $update_checkout.= ", location = '";
    $update_checkout.= $location;
    $update_checkout.= "' WHERE checkout_id = ";
    $update_checkout.= $checkout_id;
    $update_checkout_result = $mysqli->query($update_checkout);
    return $update_checkout_result;
}


function select_user_product($mysqli, $user_id, $product_id) {
    $select_user_product = "SELECT * FROM user_product WHERE user_id = ";
    $select_user_product.= $user_id;
    $select_user_product.= " AND product_id = ";
    $select_user_product.= $product_id;

    return $mysqli->query($select_user_product);

}

function select_password_reset_key($mysqli, $email, $password_reset_key) {
    $select_password_reset_key = "SELECT * FROM password_reset_key 
                                    INNER JOIN users 
                                    ON users.user_id = password_reset_key.user_id
                                    WHERE users.email = '";
    $select_password_reset_key.= $email;
    $select_password_reset_key.= "' AND password_reset_key.password_reset_key = '";
    $select_password_reset_key.= $password_reset_key;
    $select_password_reset_key.= "' LIMIT 1";
    $select_password_reset_key_result = $mysqli->query($select_password_reset_key);
    return $select_password_reset_key;
}

function update_user_password($mysqli, $user_id, $password) {
    $update_user_password = "UPDATE users SET password = '";
    $update_user_password.= $password;
    $update_user_password.= "' WHERE user_id = ";
    $update_user_password.= $user_id;

    $update_user_password_result = $mysqli->query($update_user_password);
    return $update_user_password_result;

}

function delete_password_reset_key($mysqli, $user_id) {
    $delete_password_reset_key = "DELETE FROM password_reset_key WHERE user_id = ";
    $delete_password_reset_key.= $user_id;

    $delete_password_reset_key_result = $mysqli->query($delete_password_reset_key);
    return $delete_password_reset_key_result;
}

function insert_password_reset_key($mysqli, $user_id, $password_reset_key) {
    $insert_password_reset_key = "INSERT INTO password_reset_key (user_id, password_reset_key) VALUES (";
    $insert_password_reset_key.= $user['user_id'];
    $insert_password_reset_key.= ", '";
    $insert_password_reset_key.= $password_reset_key;
    $insert_password_reset_key.= "')";

    $insert_password_reset_key_result = $mysqli->query($insert_password_reset_key);
    return $insert_password_reset_key_result;
}


function update_cart_product($mysqli, $user_id, $product_id, $quantity) {
    $update_cart_product = "UPDATE cart_product SET quantity = quantity + ";
    $update_cart_product.= $quantity;
    $update_cart_product.= " WHERE user_id = ";
    $update_cart_product.= $user['user_id'];
    $update_cart_product.= " AND product_id = ";
    $update_cart_product.= $product_id;

    $update_cart_product_result = $mysqli->query($update_cart_product);
    return $update_cart_product_result;
}

function insert_cart_product($mysqli, $user_id, $product_id, $quantity) {
    $insert_cart_product = "INSERT INTO cart_product (product_id, user_id, quantity) VALUES (";
    $insert_cart_product.= $product_id;
    $insert_cart_product.= ", ";
    $insert_cart_product.= $user_id;
    $insert_cart_product.= ", ";
    $insert_cart_product.= $quantity;
    $insert_cart_product.= ")";

    $insert_cart_product_result = $mysqli->query($insert_cart_product);
    return $insert_cart_product_result;
}

function select_checkouts_by_user_payment_confirmed($mysqli, $user_id, $payment_confirmed) {
    $select_user_checkout = "SELECT * FROM checkouts 
                                INNER JOIN user_checkout
                                ON user_checkout.checkout_id = checkouts.checkout_id
                                WHERE user_checkout.user_id = ";
    $select_user_checkout.= $user_id;
    $select_user_checkout.= " AND checkouts.payment_confirmed = ";
    $select_user_checkout.= $payment_confirmed;

    $select_user_checkout_result = $mysqli->query($select_user_checkout);
    return $select_user_checkout_result;
}

function update_checkout_payment_confirmed($mysqli, $checkout_id, $payment_confirmed) {
    $update_checkout = "UPDATE checkouts SET payment_confirmed = ";
    $update_checkout.= $payment_confirmed;
    $update_checkout.= " WHERE checkout_id = ";
    $update_checkout.= $checkout['checkout_id'];
    $update_checkout_result = $mysqli->query($update_checkout);

    return $update_checkout_result;
}

function exists_cart_product($mysqli, $user_id, $product_id) {
    $select_cart_product = "SELECT * FROM cart_product WHERE user_id = ";
    $select_cart_product.= $user_id;
    $select_cart_product.= " AND product_id = ";
    $select_cart_product.= $product_id;
    $select_cart_product_result = $mysqli->query($select_cart_product);
    return ($select_cart_product_result->num_rows > 0);
}

?>
