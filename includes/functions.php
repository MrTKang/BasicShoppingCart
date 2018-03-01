<?php

require("includes/connection.php");
require("includes/credentials.php");
require_once("vendor/autoload.php");

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

function login() {
	return isset($_SESSION['user']);
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

//SIGNUP

function sign_up_user($mysqli, $gmail_account, $gmail_password) {
	$redirect_to_login = FALSE;
	$error_message = "";
	if (isset($_POST['submit']) && $_POST['password'] == $_POST['passwordagain']){
		$select_user = "SELECT * FROM users WHERE email='";
		$select_user.= $_POST['email'];
		$select_user.= "'";
		$select_user_result = $mysqli->query($select_user);

		if ($select_user_result->num_rows == 0) {
		    $insert_user = "INSERT INTO users (name, email, password) VALUES ('";
		    $insert_user.= $_POST['name'];
		    $insert_user.= "', '";
		    $insert_user.= $_POST['email'];
		    $insert_user.= "', MD5('";
		    $insert_user.= $_POST['password'];
		    $insert_user.= "'))";

		    if ($mysqli->query($insert_user)===TRUE) {
		        $redirect_to_login = TRUE;
		    } else {
		        $message = $mysqli->error;
		    }

		    $user_id = $mysqli->insert_id;

		    //Make Confirmation Key
		    $confirmation_key = md5($_POST['name'].$_POST['email'].date("Ymd"));
		    //Save it to database
		    $insert_confirmation_key = "INSERT INTO confirmation_key (user_id, confirmation_key, email) VALUES (";
		    $insert_confirmation_key.= $user_id;
		    $insert_confirmation_key.= ", '";
		    $insert_confirmation_key.= $confirmation_key;
		    $insert_confirmation_key.= "', '";
		    $insert_confirmation_key.= $_POST['email'];
		    $insert_confirmation_key.= "')";

		    $insert_confirmation_key_result = $mysqli->query($insert_confirmation_key);
		    
		    //Set up template
		    if ($insert_confirmation_key_result === TRUE) {

		        $template = file_get_contents("signup_email_confirmation_template.txt");
		        $template = str_replace('{EMAIL}', $_POST['email'], $template);
		        $template = str_replace('{KEY}', $confirmation_key, $template);
		        $template = str_replace('{ADDRESS}', "http://localhost", $template);

		        //Send Email
		        $transport = new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
		        $transport->setUsername($gmail_account);
		        $transport->setPassword($gmail_password);
		        $mailer = new Swift_Mailer($transport);

		        $email_message = new Swift_Message("Welcome to Kevin's Store");
		        $email_message->setFrom(['freestore0202@gmail.com' => "Kevin's Store"]);
		        $email_message->setTo([$_POST['email'] => $_POST['name']]);
		        $email_message->setBody($template, 'text/html');

		        $send_result = $mailer->send($email_message);

		        $message = "Please check your email";
		    }

		} else {
			$error_message.= "The email you submitted is already in use";
			$redirect_to_login = FALSE;
		}



	} else if (isset($_POST['submit']) && $_POST['password'] != $_POST['passwordagain']) {
	    $error_message.= "passwords are not matching";
		$redirect_to_login = FALSE;
	}

	if ($redirect_to_login) {
	     header("Location: login.php");
	}

	return array("error" => $error_message, "message" => $message);
}

//LOGIN

function login_user($mysqli) {
	if (isset($_POST['submit'])) {
	    $select_user = "SELECT * FROM users WHERE email = '";
	    $select_user.= $_POST['email'];
	    $select_user.= "'";

	    $select_user_result = $mysqli->query($select_user);
	    if ($select_user_result->num_rows == 1) {
	        $login_user = $select_user_result->fetch_array();

	        if ($login_user['password'] === md5($_POST['password']) && $login_user['email_confirmed'] == 1) {
	            $_SESSION['user'] = $login_user;
	            header("Location: index.php");
	        } else if ($login_user['password'] === md5($_POST['password']) && $login_user['email_confirmed'] == 0) {
	            $error_message = "please confirm your email";
	        } else {
	            $error_message = "wrong password";
	        }
	    }
	}
}

function confirm_user_email($mysqli) {
	if (isset($_GET['email']) && isset($_GET['key'])) {
	    $select_confirmation_key = "SELECT * FROM confirmation_key WHERE email ='";
	    $select_confirmation_key.= $_GET['email'];
	    $select_confirmation_key.= "' AND confirmation_key = '";
	    $select_confirmation_key.= $_GET['key'];
	    $select_confirmation_key.= "' LIMIT 1";
	    $select_confirmation_key_result = $mysqli->query($select_confirmation_key);


	    if ($select_confirmation_key_result->num_rows != 0) {
	        $user_id = $select_confirmation_key_result->fetch_array()['user_id'];
	        $delete_confirmation_key = "DELETE FROM confirmation_key WHERE user_id =";
	        $delete_confirmation_key.= $user_id;
	        $delete_confirmation_key.= " LIMIT 1";
	        $delete_confirmation_key_result = $mysqli->query($delete_confirmation_key);


	        if ($delete_confirmation_key_result === TRUE) {
	            $update_user = "UPDATE users SET email_confirmed = 1 WHERE user_id = ";
	            $update_user.= $user_id;

	            $update_user_result = $mysqli->query($update_user);
	        }

	        $error_message = $mysqli->error;
	    }
	}
}

//MYACCOUNT

function display_my_checkouts($mysqli) {

    if (isset($_SESSION['user'])) {
        $select_user_checkouts = "SELECT user_checkout.checkout_id, checkouts.created_at FROM user_checkout ";
        $select_user_checkouts.= "INNER JOIN checkouts ";
        $select_user_checkouts.= "ON checkouts.checkout_id = user_checkout.checkout_id ";
        $select_user_checkouts.= "WHERE user_checkout.user_id=";
        $select_user_checkouts.= $_SESSION['user']['user_id'];

        $select_user_checkouts_result = $mysqli->query($select_user_checkouts);

        while ($checkout = $select_user_checkouts_result->fetch_array()){
            $select_products = "SELECT * FROM products ";
            $select_products.= "INNER JOIN product_checkout ";
            $select_products.= "ON products.product_id=product_checkout.product_id ";
            $select_products.= "WHERE product_checkout.checkout_id=";
            $select_products.= $checkout['checkout_id'];

            $select_products_result = $mysqli->query($select_products);
        	$checkout_card = '<div class="card">
            <div class="card-header">
            	{CREATED_AT}
            </div>
            <div class="card-body">
            	<blockquote class="blockquote mb-0">
            		{PRODUCTS}
            	</blockquote>
        	</div>';

        	$product_items = "";
        	$total_price = 0;
            while ($product = $select_products_result->fetch_array()) {
                $product_price = number_format($product['price'] * $product['quantity'], 2, '.', '');
                $total_price+= $product_price;
                $product_item = '<p> {PRODUCT_NAME} x {PRODUCT_QUANTITY} = ${PRODUCT_PRICE}</p>';
                $product_search = array("{PRODUCT_NAME}", "{PRODUCT_QUANTITY}", "{PRODUCT_PRICE}");
                $product_replace = array($product['name'], $product['quantity'], $product_price);
                $product_item = str_replace($product_search, $product_replace, $product_item);

                $product_items.= $product_item;
            }
            $checkout_price = '<p>Total Price: ${TOTAL_PRICE}</p>';
            $checkout_price = str_replace("{TOTAL_PRICE}", number_format($total_price, 2, '.', ''), $checkout_price);
            $product_items.= $checkout_price;

            $checkout_search = array("{CREATED_AT}", "{PRODUCTS}");
            $checkout_replace = array($checkout['created_at'], $product_items);
            $checkout_card = str_replace($checkout_search, $checkout_replace, $checkout_card);
            echo($checkout_card);
        }
    }
}

//MYPRODUCTS

function display_my_products($mysqli) {
	if (isset($_SESSION['user'])) {
	    $select_user_product = "SELECT * FROM user_product ";
	    $select_user_product.= "INNER JOIN products ";
	    $select_user_product.= "ON user_product.product_id = products.product_id ";
	    $select_user_product.= "WHERE user_product.user_id = ";
	    $select_user_product.= $_SESSION['user']['user_id'];

	    $select_user_product_result = $mysqli->query($select_user_product);

	    while ($product = $select_user_product_result->fetch_array()) {
	        $product_item = '<p>{PRODUCT_NAME}</p>';
	        $product_item = str_replace("{PRODUCT_NAME}", $product['name'], $product_item);
	        echo($product_item);
	    } 
	}
}

function create_product($mysqli) {
	if (isset($_SESSION['user']) && isset($_POST['product'])) {
	    $target_dir = "images/";
	    $target_file = $target_dir.basename($_FILES["upload"]["name"]);

	    $insert_product = "INSERT INTO products (name, description, price, image) VALUES ('";
	    $insert_product.= $_POST['name'];
	    $insert_product.= "', '";
	    $insert_product.= $_POST['description'];
	    $insert_product.= "', ";
	    $insert_product.= $_POST['price'];
	    $insert_product.= ", '";
	    $insert_product.= $target_file;
	    $insert_product.= "')";

	    $insert_product_result = $mysqli->query($insert_product);
	    $product_id = $mysqli->insert_id;

	    $insert_user_product = "INSERT INTO user_product (user_id, product_id) VALUES (";
	    $insert_user_product.= $_SESSION['user']['user_id'];
	    $insert_user_product.= ",";
	    $insert_user_product.= $product_id;
	    $insert_user_product.= ")";

	    $insert_user_product_result = $mysqli->query($insert_user_product);

	    $select_category_id = "SELECT category_id FROM categories WHERE name = '";
	    $select_category_id.= $_POST['category'];
	    $select_category_id.= "'";

	    $select_category_id_result = $mysqli->query($select_category_id);

	    $category = $select_category_id_result->fetch_array();
	    $category_id = $category['category_id'];

	    $insert_category_product = "INSERT INTO category_product(category_id, product_id) VALUES (";
	    $insert_category_product.= $category_id;
	    $insert_category_product.= ", ";
	    $insert_category_product.= $product_id;
	    $insert_category_product.= ")";

	    $insert_category_product_result = $mysqli->query($insert_category_product);

	    $upload_check = 1;
	    $check = getimagesize($_FILES["upload"]["tmp_name"]);

	    if($check !== false) {
	        echo "File is an image - " . $check["mime"] . ".";
	        $upload_check = 1;
	    } else {
	        echo "File is not an image.";
	        $upload_check = 0;
	    }

	    if (move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file)) {
	        echo "The file ". basename( $_FILES["upload"]["name"]). " has been uploaded.";
	    } else {
	        echo "Sorry, there was an error uploading your file.";
	    } 
	}
}

function display_category_form($mysqli) {
    $select_categories = "SELECT * FROM categories";
    $select_categories_result = $mysqli->query($select_categories);
    while ($category = $select_categories_result->fetch_array()){
        $category_options = '<option value="{NAME}">{NAME}</option>';
        $category_options = str_replace("{NAME}", $category['name'], $category_options);
        echo($category_options);
    }
}

//MYCATEGORY

function create_category($mysqli) {
	if (isset($_SESSION['user']) && isset($_POST['category'])) {
	    $insert_category = "INSERT INTO categories (name) VALUES ('";
	    $insert_category.= $_POST['name'];
	    $insert_category.= "')";

	    $insert_category_result = $mysqli->query($insert_category);
	}
}

//CHECKOUT

function display_checkout_cart($mysqli) {
    if (isset($_SESSION['cart'])) {
        $select_products = "SELECT * FROM products WHERE product_id IN (";
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $select_products.= $product_id;
            $select_products.= ", ";
        }
        $select_products = substr($select_products, 0, -2);
        $select_products.= ")";
        $select_products_result = $mysqli->query($select_products);
        $total_price = 0;
        while ($product = $select_products_result->fetch_array()) {
            $quantity = $_SESSION['cart'][$product['product_id']]['quantity'];
            $total_price+= $product['price'] * $quantity;
            $product_item = '<p>{PRODUCT_NAME} X {QUANTITY} = ${PRICE}</p>';

            $product_search = array("{PRODUCT_NAME}", "{QUANTITY}", "{PRICE}");
            $product_replace = array($product['name'], $quantity, number_format($product['price'] * $quantity , 2, '.', ''));
            $product_item = str_replace($product_search, $product_replace, $product_item);

			echo($product_item);        
        }
        $total_price_line = '<p>Total Price: ${TOTAL_PRICE}</p>';
        $total_price_line = str_replace("{TOTAL_PRICE}", number_format($total_price, 2, '.', ''), $total_price_line);
        echo($total_price_line);
    }
}

//CHECKOUT COMPLETE

function complete_checkout($mysqli) {
	if (isset($_POST['checkout']) && isset($_SESSION['user'])) {

	    $insert_checkout = "INSERT INTO checkouts (address, postal_code) VALUES ('";
	    $insert_checkout.= $_POST['address'];
	    $insert_checkout.= "', '";
	    $insert_checkout.= $_POST['postalcode'];
	    $insert_checkout.= "')";
	    $insert_checkout_result = $mysqli->query($insert_checkout);

	    $checkout_id = $mysqli->insert_id;

	    $message = "";

	    foreach ($_SESSION['cart'] as $product_id => $product) {
	        $insert_product_checkout = "INSERT INTO product_checkout (product_id, checkout_id, quantity) VALUES (";
	        $insert_product_checkout.= $product_id;
	        $insert_product_checkout.= ",";
	        $insert_product_checkout.= $checkout_id;
	        $insert_product_checkout.= ",";
	        $insert_product_checkout.= $product['quantity'];
	        $insert_product_checkout.= ")";

	        $insert_product_checkout_result = $mysqli->query($insert_product_checkout);
	    }

	    $insert_user_checkout = "INSERT INTO user_checkout (user_id, checkout_id) VALUES (";
	    $insert_user_checkout.= $_SESSION['user']['user_id'];
	    $insert_user_checkout.= ", ";
	    $insert_user_checkout.= $checkout_id;
	    $insert_user_checkout.= ")";

	    $insert_user_checkout_result = $mysqli->query($insert_user_checkout);

	    unset($_SESSION['cart']);
	}
}

?>