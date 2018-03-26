<?php

require("includes/connection.php");
require("includes/credentials.php");
require_once("vendor/autoload.php");
require("includes/dbfunctions.php");

//PRODUCTS
function display_products($mysqli) {
	$select_products = "SELECT * FROM products WHERE available = 1 ORDER BY name ASC";
	$select_products_result = $mysqli->query($select_products);

	while ($product = $select_products_result->fetch_array()) {
		display_product($product);
	}  
}

function display_products_from_category($mysqli, $category) {
	$select_products_result = select_products_by_category($mysqli, $category);

	while ($product = $select_products_result->fetch_array()) {
		display_product($product);
	}  
}


function display_product($product){
	$product_div = '
	<div class="product-card">
		<div class="product-padding-container">
			<div class="product-image-container">
				<a href="productdetails.php?product_id={PRODUCT_ID}">
					<img class="product-image" alt="Thumbnail" src="{IMAGE}">
				</a>
			</div>
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

function add_to_cart($mysqli, $product_id, $session) {
	if (isset($session['user'])) {
		$select_cart_products_result = select_cart_product_by_user_product($mysqli, $session['user']['user_id'], $product_id);
		if ($select_cart_products_result->num_rows > 0) {
			$update_cart_product = "UPDATE cart_product SET quantity = quantity + 1";
			$update_cart_product_result = $mysqli->query($update_cart_product);
		} else {
			$insert_cart_product_result = insert_cart_product($mysqli, $session['user']['user_id'], $product_id,  1);
		}

	} else if (isset($session['cart'][$product_id])) {
		$session['cart'][$product_id]['quantity']++;
	} else {
		$select_product_result = select_product($mysqli, $product_id);
		if ($select_product_result->num_rows != 0) {
			$product = $select_product_result->fetch_array();
			$session['cart'][$product['product_id']] = array("quantity" => 1, "price" => $product['price']);
		}
	}
	return $session;
}

//INDEX
function edit_session_cart($cart, $post_quantities) {
	foreach($post_quantities as $key => $value) {
		if ($value == 0) {
			unset($cart[$key]);
		} else {
			$cart[$key]['quantity'] = $value;
		}
	}
}

function edit_user_cart($mysqli, $user, $post_quantities) {
	$select_cart_products_result = select_cart_products($mysqli, $user['user_id'], $post_quantities);
	while ($cart_product = $select_cart_products_result->fetch_array()) {
		if ($post_quantities[$cart_product['product_id']] == 0) {
			delete_cart_product($mysqli, $user['user_id'], $cart_product['product_id']);
		} else {
			update_cart_product($mysqli, $user['user_id'], $product_id, $quantity);
		}
	}
}


function is_logged_in() {
	if (isset($_GET['logout'])) {
		unset($_SESSION['user']);
		unset($_SESSION['cart']);
	}
	return isset($_SESSION['user']);
}

function display_categories($mysqli, $categoryname) {
	$select_categories = "SELECT * FROM categories";
	$categories_result = $mysqli->query($select_categories);
	echo('<li><a href="index.php">All</a></li>');
	while ($category = $categories_result->fetch_array()) {
		if ($categoryname == $category['name']) {
			$category_anchor = '<li class="selected-category"><a href="index.php?category={CATEGORY}">{CATEGORY}</a></li>';
		} else {
			$category_anchor = '<li><a href="index.php?category={CATEGORY}">{CATEGORY}</a></li>';
		}
		$category_anchor = str_replace("{CATEGORY}", $category['name'], $category_anchor);
		echo($category_anchor);
	}
}

function display_user_cart($mysqli, $user) {
	$select_cart_products_result = select_cart_product($mysqli, $user['user_id']);

	$cart_products = array();

	while ($cart_product = $select_cart_products_result->fetch_array()) {
		array_push($cart_products, $cart_product);
	}
	if ($select_cart_products_result->num_rows == 0) {
		display_empty_cart();
	}
	display_cart($cart_products);
}

function display_session_cart($mysqli, $cart) {
	$products_result = select_products_from_cart($mysqli, $cart);

	$products = array();
	while ($product = $products_result->fetch_array()) {
		$product['quantity'] = $cart[$product['product_id']]['quantity'];
		array_push($products, $product);
	}

	display_cart($products);
}


function display_cart($products) {
	$checkout_price = 0;
	foreach ($products as $product){
		$quantity = $product['quantity'];
		$checkout_price += $product['price'] * $quantity;

		$cart_item = '<p>{PRODUCT_NAME} X <input type="text" name="quantity[{PRODUCT_ID}]" value="{QUANTITY}" size="5"/> = ${PRICE}</p>';
		$search = array("{PRODUCT_NAME}", "{PRODUCT_ID}", "{QUANTITY}", "{PRICE}");
		$replace = array($product['name'], $product['product_id'], $quantity, number_format($product['price'] * $quantity , 2, '.', ''));
		$cart_item = str_replace($search, $replace, $cart_item);
		echo($cart_item);
	}
}

function display_empty_cart() {
	echo('<p>Your Cart is empty. Please add some products.</p>');
}

//SIGNUP

function sign_up_user($mysqli, $gmail_account, $gmail_password, $post) {
	$signed_up = FALSE;
	$error_message = "";
	$message = "";

	if (isset($post['submit']) && $post['password'] != $post['passwordagain']) {
	    return array('message' => "could not process", 
	    	'error' => "passwords are not matching",
	    	'signed_up' => FALSE);

	} else if (isset($post['submit']) && $post['password'] == $post['passwordagain']){
	    if (strlen($post['password']) < 8) {
	        $error_message.= "Your Password Must Contain At Least 8 Characters!";
	    }
	    else if (!preg_match("#[0-9]+#",$post['password'])) {
	        $error_message.= "Your Password Must Contain At Least 1 Number!";
	    } else {
			$select_user_result = select_user_by_email($mysqli, $post['email']);
			if ($select_user_result->num_rows != 0){
		    	return info_array("could not process", "The email you submitted is already in use", FALSE);
			} else {
				$insert_user_result = insert_user($mysqli, $post['name'], $post['email'], $post['password']);

			    if (!$insert_user_result) {
		    		return info_array("could not process", $mysqli->error, FALSE);	    		
			    }
			    $user_id = $mysqli->insert_id;

			    $confirmation_key = md5($post['name'].$post['email'].date("Ymd"));
			    $insert_confirmation_key_result = insert_confirmation_key($mysqli, $user_id, $confirmation_key, $post['email']);
			    
			    if ($insert_confirmation_key_result) {
					$template = file_get_contents("signup_email_confirmation_template.txt");
			    	send_email($post['email'], $post['name'], $confirmation_key, $gmail_account, $gmail_password, $template, "Welcome to Kevin's Store");
			        $message.= "Please check your email at ";
			        $message.= $post['email'];
			        $signed_up = TRUE;
			    }
			} 
		}
	}
	if (isset($post['resend'])) {
		$signed_up = TRUE;
	}

	return array('message' => $message, 
    	'error' => $error_message,
		'signed_up' => $signed_up,
		'name' => $post['name'],
		'email' => $post['email']);
}

function send_email($email, $name, $key, $gmail_account, $gmail_password, $email_template, $subject) {
	$template = str_replace('{EMAIL}', $email, $email_template);
	$template = str_replace('{KEY}', $key, $template);
	$template = str_replace('{ADDRESS}', "http://localhost", $template);

	//Send Email
	$transport = new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
	$transport->setUsername($gmail_account);
	$transport->setPassword($gmail_password);
	$mailer = new Swift_Mailer($transport);

	$email_message = new Swift_Message($subject);
	$email_message->setFrom(['freestore0202@gmail.com' => "Kevin's Store"]);
	$email_message->setTo([$email => $name]);
	$email_message->setBody($template, 'text/html');

	$send_result = $mailer->send($email_message);
}

function resend_email($mysqli, $user_email, $gmail_account, $gmail_password) {
	$select_confirmation_key_result = select_confirmation_key_by_email($mysqli, $user_email);

	$confirmation_key = $select_confirmation_key_result->fetch_array();
	$template = file_get_contents("signup_email_confirmation_template.txt");
	send_email($_SESSION['confirmation_email'], $_SESSION['username'], $confirmation_key['confirmation_key'], $gmail_account, $gmail_password, $template, "Welcome to Kevin's Store");
}

function display_sign_up_form($status) {
    $error_message = str_replace("{ERROR}", $status['error'], '<h6>{ERROR}</h6>');
    $message = str_replace("{MESSAGE}", $status['message'], '<h6>{MESSAGE}</h6>');

    echo($error_message);
    echo($message);

    if ($status['signed_up']) {
    	$_SESSION['confirmation_email'] = $_POST['email'];
    	$_SESSION['username'] = $_POST['name'];
    	$resend_button = '<button class="btn btn-lg btn-primary btn-block" name="resend" type="submit">Resend Email</button>';
    	echo($resend_button);
    } else {
	    $sign_up_form = '<label for="name">Your Name</label>
	    <input type="text" class="form-control" name="name" required="" value="{NAME}" autofocus="">
	    <label for="email">Email address</label>
	    <input type="email" class="form-control" name="email" required="" value="{EMAIL}" autofocus="">
	    <label for="password">Password</label>
	    <input type="password" class="form-control" name="password" required="">
	    <label for="passwordagain">Password Again</label>
	    <input type="password" class="form-control" name="passwordagain" required="">
	    <button id="sign-up-btn" class="btn btn-lg btn-primary btn-block"  name="submit" type="submit">Sign up</button>';

	    $search = array("{NAME}", "{EMAIL}");
	    $replace = array("", "");
	    if (isset($status['name']) && isset($status['email'])) {
	    	$replace = array($status['name'], $status['email']);
	    }
	    $sign_up_form = str_replace($search, $replace, $sign_up_form);

    	echo($sign_up_form);
    }
}

//LOGIN

function transfer_cart($mysqli, $user_id, $cart) {
	$inserted = true;
	foreach ($cart as $product_id => $product) {
		if (exists_cart_product($mysqli, $user_id, $product_id)) {
			$inserted = $inserted && update_cart_product($mysqli, $user_id, $product_id, $product['quantity']);
		} else {
			$inserted = $inserted && insert_cart_product($mysqli, $user_id, $product_id, $product['quantity']);
		}
	}
	return $inserted;
}

function login_user($mysqli, $password, $email, $remember_email) {
    $select_user_result = select_user_by_email($mysqli, $email);
    if ($select_user_result->num_rows == 1) {
        $login_user = $select_user_result->fetch_array();

        if ($login_user['password'] === md5($password) && $login_user['email_confirmed'] == 1) {
            if (isset($remember_email)) {
            	setcookie("login", $email, time()+ (365 * 24 * 60 * 60));
            }

            return $login_user;
        } else if ($login_user['password'] === md5($password) && $login_user['email_confirmed'] == 0) {
            $error_message = "please confirm your email";
        } else {
            $error_message = "wrong password";
        }
    }
}

function confirm_user_email($mysqli, $email, $key) {
    $select_confirmation_key_result = select_confirmation_key_by_email_key($mysqli, $key, $email);

    if ($select_confirmation_key_result->num_rows != 0) {
        $user_id = $select_confirmation_key_result->fetch_array()['user_id'];
        $delete_confirmation_key_result = delete_confirmation_key($mysqli, $user_id);

        if ($delete_confirmation_key_result === TRUE) {
            $update_user_result = update_user_email_confirmation($mysqli, $user_id, 1);
        }

        $error_message = $mysqli->error;
    }
}

//MYACCOUNT

function display_my_checkouts($mysqli, $user) {
    $select_user_checkouts_result = select_checkouts_by_user($mysqli, $user['user_id']);

    while ($checkout = $select_user_checkouts_result->fetch_array()){
        $select_products_result = select_products_by_checkout($mysqli, $checkout['checkout_id']);

    	$checkout_card = '<div class="card">
        <div class="card-body">
        	<h5 class="card-title">Checked out at {CREATED_AT}</h6>
        	<h6 class="card-subtitle mb-2 text-muted">{SHIPMENT_STATUS}</h6>
        	{PRODUCTS}
    	</div>';

    	$shipment_status = $checkout['shipment_status'];
    	if ($checkout['location']) {
        	$shipment_status.= " at ";
        	$shipment_status.= $checkout['location'];
    	}

    	$product_items = "";
    	$total_price = 0;
        while ($product = $select_products_result->fetch_array()) {
            $product_price = number_format($product['price'] * $product['quantity'], 2, '.', '');
            $total_price+= $product_price;
            $product_item = '<p class="card-text"> {PRODUCT_NAME} x {PRODUCT_QUANTITY} = ${PRODUCT_PRICE}</p>';
            $product_search = array("{PRODUCT_NAME}", "{PRODUCT_QUANTITY}", "{PRODUCT_PRICE}");
            $product_replace = array($product['name'], $product['quantity'], $product_price);
            $product_item = str_replace($product_search, $product_replace, $product_item);

            $product_items.= $product_item;
        }
        $checkout_price = '<p class="card-text">Total Price: ${TOTAL_PRICE}</p>';
        $checkout_price = str_replace("{TOTAL_PRICE}", number_format($total_price, 2, '.', ''), $checkout_price);
        $product_items.= $checkout_price;

        $checkout_search = array("{CREATED_AT}", "{SHIPMENT_STATUS}", "{PRODUCTS}");
        $checkout_replace = array($checkout['created_at'], $shipment_status, $product_items);
        $checkout_card = str_replace($checkout_search, $checkout_replace, $checkout_card);
        echo($checkout_card);
    }
}

//MYPRODUCTS

function add_inventory($mysqli, $product_id, $quantity) {
	$select_product_result = select_product($mysqli, $product_id);

	$product = $select_product_result->fetch_array();

	$update_product_result = update_product_inventory($mysqli, $product['inventory'] + $quantity, $product_id);
}

function display_my_product($product) {
    $product_item = '<div class="card my-product-item"> <div class="card-body">
    <h6 class="card-title my-product-name">{NAME}</h6>
    <p class="card-subtitle mb-2 text-muted">${PRICE}</p>
    <form id="edit_inventory_form" class="edit-inventory-form" method="post" action="myproducts.php?product_id={PRODUCT_ID}"> 
    	<p class="card-subtitle mb-2 text-muted inventory-text">{INVENTORY} in inventory</p>
    	<input class="inventory-input form-control" type="number" name="inventory" value=0>
    	<button class="inventory-btn btn btn-lg btn-primary btn-block"  name="add_inventory" type="submit">Add</button>
    </form>
    <p class="card-text">{DESCRIPTION}</p>';

    if ($product['available'] == 1){
    	$product_item.= '<a href="myproducts.php?edit_product={PRODUCT_ID}&availability=0" class="card-link my-product-delete">stop selling</a>';
    	$product_item.= '<p class="card-link to-edit-tag"> to edit, stop selling first </p></div></div>';
    } else {
    	$product_item.= '<a href="myproducts.php?edit_product={PRODUCT_ID}&availability=1" class="card-link my-product-delete">start selling</a>';
   	 	$product_item.= '<a class="card-link" href="editproduct.php?product_id={PRODUCT_ID}"> edit </a></div></div>';
    }	        
    $search = array("{NAME}", "{PRICE}", "{INVENTORY}", "{DESCRIPTION}", "{PRODUCT_ID}");
    $replace = array($product['name'], $product['price'], $product['inventory'], $product['description'], $product['product_id']);
    $product_item = str_replace($search, $replace, $product_item);
    echo($product_item);
}

function display_all_products($mysqli) {
	$select_products = "SELECT * FROM products";
	$select_products_result = $mysqli->query($select_products);
	while ($product = $select_products_result->fetch_array()) {
		display_my_product($product);
	}
}

function display_my_products($mysqli, $user_id) {
    $select_user_product_result = select_products_by_user($mysqli, $user_id);

    while ($product = $select_user_product_result->fetch_array()) {
 		display_my_product($product);
    } 
}

function create_product($mysqli, $user, $product_post, $filename, $tempfilename) {
    $target_dir = "images/";
    $target_file = $target_dir.basename($filename);

    $upload_check = 1;
    $check = getimagesize($tempfilename);

    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $upload_check = 1;
    } else {
        echo "File is not an image.";
        $upload_check = 0;
    }

    if (move_uploaded_file($tempfilename, $target_file)) {
        echo "The file ". basename($filename). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    } 
    $insert_product_result = insert_product($mysqli, $product_post['name'], $product_post['description'], $product_post['price'], $target_file);
    $product_id = $mysqli->insert_id;

    $insert_user_product_result = insert_user_product($mysqli, $user['user_id'], $product_id);

    $select_category_id_result = select_category_by_name($mysqli, $product_post['category']);

    $category = $select_category_id_result->fetch_array();
    $category_id = $category['category_id'];

    $insert_category_product_result = insert_category_product($mysqli, $category_id, $product_id);

}
//?????
function set_product_availability($mysqli, $product_id, $availability){
	$update_product = "UPDATE products SET available = ";
	$update_product.= $availability;
	$update_product.= " WHERE product_id = ";
	$update_product.= $product_id;

	$update_product_result = $mysqli->query($update_product);
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


//EDITPRODUCT
//TODO
function edit_product($mysqli, $product_id, $product_info) {
	$update_product_result = update_product($mysqli, $product_id, $product_info['name'], $product_info['price'], $description);
	return $update_product_result;
}


function display_edit_product_form($mysqli, $product_id) {
	$select_product_result = select_product($mysqli, $product_id);

	$product = $select_product_result->fetch_array();

    $edit_product_form = '<form id="edit_product_form" class="edit-product-form" method="post" action="editproduct.php?product_id={PRODUCT_ID}">
    <h1 class="h3 mb-3 font-weight-normal">Editing Product</h1><label for="name">Product name</label>
    <input type="text" class="form-control" name="name" required="" autofocus="" value="{NAME}">
    <label for="price">Price</label>
    <input type="number" class="form-control" name="price" required="" autofocus="" value="{PRICE}">
    <label for="description">Description</label>
    <textarea class="form-control" form="edit_product_form" name="description" required="">{DESCRIPTION}</textarea>
    <button id="sign-up-btn" class="btn btn-lg btn-primary btn-block"  name="edit" type="submit">Edit</button>
    <p class="mt-5 mb-3 text-muted">© 2017-2018</p>
    </form>';

    $search = array("{PRODUCT_ID}", "{NAME}", "{PRICE}", "{DESCRIPTION}");
    $replace = array($product_id, $product['name'], $product['price'], $product['description']);
    $edit_product_form = str_replace($search, $replace, $edit_product_form);

	echo($edit_product_form);
}

//MYCATEGORY

function create_category($mysqli) {
	if (isset($_SESSION['user']) && isset($_POST['category'])) {
	    $insert_category_result = insert_category($mysqli, $_POST['name']);
	}
}

function display_category_list($mysqli) {
	$select_categories_result = select_categories($mysqli);

	while ($category = $select_categories_result->fetch_array()) {
		$category_item = '<div class="card category-item"><div class="card-body">
	        <h6 class="card-title">{NAME}</h6>
	        <p class="card-text">{PRODUCTS}</p>';


		$select_products_result = select_products_by_category($mysqli, $category['name']);
		$products = "";
		while ($product = $select_products_result->fetch_array()) {
			$products.= $product['name'];
			$products.= ", ";
		}

		$products = substr($products, 0, -2);

        if ($category['active'] == 1){
        	$category_item.= '<a href="mycategories.php?edit_category={CATEGORY_ID}&active=0" class="card-link">deactivate</a>';
        	$category_item.= '<p class="card-link to-edit-tag"> to edit, deactivate first </p></div></div>';
        } else {
        	$category_item.= '<a href="mycategories.php?edit_category={CATEGORY_ID}&active=1" class="card-link">activate</a>';
       	 	$category_item.= '<a class="card-link" href="editcategory.php?category_id={CATEGORY_ID}"> edit </a></div></div>';
        }	    

	    $search = array("{NAME}", "{PRODUCTS}", "{CATEGORY_ID}");
	    $replace = array($category['name'], $products, $category['category_id']);
	    $category_item = str_replace($search, $replace, $category_item);

	    echo($category_item);
	}
}


function set_category_activity($mysqli, $category_id, $activity){
	$update_category = "UPDATE categories SET active = ";
	$update_category.= $activity;
	$update_category.= " WHERE category_id = ";
	$update_category.= $category_id;

	$update_category_result = $mysqli->query($update_category);
}


function display_edit_category_form($mysqli, $category_id) {
	$select_category_result = select_category($mysqli, $category_id);
	$category = $select_category_result->fetch_array();

    $edit_category_form = '<form id="edit_category_form" class="edit-category-form" method="post" action="editcategory.php?category_id={CATEGORY_ID}">
    <h1 class="h3 mb-3 font-weight-normal">Editing category</h1><label for="name">Category name</label>
    <input type="text" class="form-control" name="name" required="" autofocus="" value="{NAME}">
    <button id="sign-up-btn" class="btn btn-lg btn-primary btn-block"  name="edit" type="submit">Edit</button>
    <p class="mt-5 mb-3 text-muted">© 2017-2018</p>
    </form>';

    $search = array("{CATEGORY_ID}", "{NAME}");
    $replace = array($category_id, $category['name']);
    $edit_category_form = str_replace($search, $replace, $edit_category_form);

	echo($edit_category_form);
}

function edit_category($mysqli, $category_id, $info) {
	$update_category_result = update_category($mysqli, $category_id, $info['name']);
}
//CHECKOUT

//DEP
function display_session_checkout($mysqli, $cart) {
    $select_products_result = select_products_from_cart($mysqli, $cart);
    $total_price = 0;
    while ($product = $select_products_result->fetch_array()) {
        $quantity = $cart[$product['product_id']]['quantity'];
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

function display_user_checkout($mysqli, $user) {
	$select_products_result = select_products_by_cart_user($mysqli, $user['user_id']);

	$total_price = 0;
	while ($product = $select_products_result->fetch_array()) {
		$total_price+= $product['price'] * $product['quantity'];
        $product_item = '<p>{PRODUCT_NAME} X {QUANTITY} = ${PRICE}</p>';

        $product_search = array("{PRODUCT_NAME}", "{QUANTITY}", "{PRICE}");
        $product_replace = array($product['name'], $product['quantity'], 
        	number_format($product['price'] * $product['quantity'] , 2, '.', ''));
        $product_item = str_replace($product_search, $product_replace, $product_item);
		echo($product_item);        
	}
	$total_price_line = '<p>Total Price: ${TOTAL_PRICE}</p>';
    $total_price_line = str_replace("{TOTAL_PRICE}", number_format($total_price, 2, '.', ''), $total_price_line);
    echo($total_price_line);
}
//COMPLETECHECKOUT

function complete_checkout($mysqli, $address, $postalcode, $user) {
	$selected_products = array();
	$select_products_result = select_products_by_cart_user($mysqli, $user['user_id']);
    while ($selected_product = $select_products_result->fetch_array()){
    	if ($selected_product['inventory'] < $selected_product['quantity']) {
    		echo("not enough inventory");
    		return;
    	}
        $total_amount += $selected_product['price'] * $selected_product['quantity'];
    	array_push($selected_products, $selected_product);
    }

    delete_unconfirmed_checkouts($mysqli, $user['user_id']);

    $insert_checkout_result = insert_checkout($mysqli, $address, $postalcode, $total_amount);

    $checkout_id = $mysqli->insert_id;

    foreach ($selected_products as $product) {

    	$insert_product_checkout_result = insert_product_checkout($mysqli, $product['product_id'], $checkout_id, $product['quantity']);


        $update_product_inventory_result = update_product_inventory($mysqli, 
        								$product['inventory'] - $product['quantity'],
        								$product['product_id']);
	}

    $insert_user_checkout_result = insert_user_checkout($mysqli, $user['user_id'], $checkout_id);

    return $total_amount;
}


//MYUSERS


function display_user_list($mysqli) {
	$select_user = "SELECT * FROM users";

	$select_user_result = $mysqli->query($select_user);

	while ($user = $select_user_result->fetch_array()) {

		$select_user_checkouts_result = select_user_checkouts($mysqli, $user['user_id']);

		$user_item = '<div class="card user-item"><div class="card-body">
	        <h6 class="card-title">{NAME}</h6>';
		$user_item = str_replace("{NAME}", $user['name'], $user_item);

		while ($checkout = $select_user_checkouts_result->fetch_array()) {
			$checkout_link = '<a href="editcheckout.php?checkout_id={CHECKOUT_ID}" class="card-link checkout-link"class="card-text">{CHECKOUT_INFO}</a>';
			$checkout_info = $checkout['created_at'];
			$checkout_info.= ", ";
			$checkout_info.= $checkout['shipment_status'];
			if ($checkout['location']) {
				$checkout_info.= " at ";
				$checkout_info.= $checkout['location'];	
			}
			$checkout_link = str_replace("{CHECKOUT_INFO}", $checkout_info, $checkout_link);
			$checkout_link = str_replace("{CHECKOUT_ID}", $checkout['checkout_id'], $checkout_link);
			$user_item.= $checkout_link;
		}

        if ($user['active'] == 1){
        	$user_item.= '<a href="myusers.php?edit_user={USER_ID}&active=0" class="card-link">deactivate</a>';
        	$user_item.= '<p class="card-link to-edit-tag"> to edit, deactivate first </p></div></div>';
        } else {
        	$user_item.= '<a href="myusers.php?edit_user={USER_ID}&active=1" class="card-link">activate</a>';
       	 	$user_item.= '<a class="card-link" href="edituser.php?user_id={USER_ID}"> edit </a></div></div>';
        }	    

	    $search = array("{NAME}", "{USER_ID}");
	    $replace = array($user['name'], $user['user_id']);
	    $user_item = str_replace($search, $replace, $user_item);

	    echo($user_item);
	}
}

//??
function set_user_activity($mysqli, $user_id, $activity){
	$update_user = "UPDATE users SET active = ";
	$update_user.= $activity;
	$update_user.= " WHERE user_id = ";
	$update_user.= $user_id;

	$update_user_result = $mysqli->query($update_user);
}


//EDITUSER

function check_permission_bit($permission_bit, $user_permission) {
	return ($permission_bit & $user_permission ? "checked" : ""); 
}

function display_edit_user_form($mysqli, $user_id) {

	$select_user = "SELECT * FROM users WHERE user_id = ";
	$select_user.= $user_id;
	$select_user.= " LIMIT 1";

	$select_user_result = $mysqli->query($select_user);

	$user = $select_user_result->fetch_array();

	$add_products = check_permission_bit(1, $user['permissions']);
	$delete_products = check_permission_bit(2, $user['permissions']);
	$edit_products = check_permission_bit(4, $user['permissions']);
	$delete_own_products = check_permission_bit(8, $user['permissions']);
	$edit_own_products = check_permission_bit(16, $user['permissions']);
	$add_categories = check_permission_bit(32, $user['permissions']);
	$delete_categories = check_permission_bit(64, $user['permissions']);
	$edit_categories = check_permission_bit(128, $user['permissions']);
	$add_users = check_permission_bit(256, $user['permissions']);
	$delete_users = check_permission_bit(512, $user['permissions']);
	$edit_users = check_permission_bit(1024, $user['permissions']);


    $edit_user_form = '<form id="edit_user_form" class="edit-user-form" method="post" action="edituser.php?user_id={USER_ID}">
    <h1 class="h3 mb-3 font-weight-normal">Editing User</h1>
    <label for="name">User name</label>
    <input type="text" class="form-control" name="name" required="" autofocus="" value="{NAME}">
    <label for="name">Email</label>
    <input type="text" class="form-control" name="email" required="" autofocus="" value="{EMAIL}">
	<div class="form-check">
		<input class="form-check-input" type="checkbox" name="permissions[]" value=1 {ADD_PRODUCTS}>
		<label class="form-check-label" for="add-products">ADD PRODUCTS</label>
	</div>
	<div class="form-check">
		<input class="form-check-input" type="checkbox" name="permissions[]" value=2 {DELETE_PRODUCTS}>
		<label class="form-check-label" for="delete-products">DELETE PRODUCTS</label>
	</div>
	<div class="form-check">
  		<input class="form-check-input" type="checkbox" name="permissions[]" value=4 {EDIT_PRODUCTS}>
  		<label class="form-check-label" for="edit-products">EDIT PRODUCTS</label>
	</div>
	<div class="form-check">
		<input class="form-check-input" type="checkbox" name="permissions[]" value=8 {DELETE_OWN_PRODUCTS}> 
		<label class="form-check-label" for="delete-own-products">DELETE OWN PRODUCTS</label>
	</div>
	<div class="form-check">
		<input class="form-check-input" type="checkbox" name="permissions[]" value=16 {EDIT_OWN_PRODUCTS}>
		<label class="form-check-label" for="edit-own-products">EDIT OWN PRODUCTS</label>
	</div>
	<div class="form-check">
  		<input class="form-check-input" type="checkbox" name="permissions[]" value==32 {ADD_CATEGORIES}>
  		<label class="form-check-label" for="add-category">ADD CATEGORY</label>
	</div>
	<div class="form-check">
		<input class="form-check-input" type="checkbox" name="permissions[]" value=64 {DELETE_CATEGORIES}>
		<label class="form-check-label" for="delete-category">DELETE CATEGORY</label>
	</div>
	<div class="form-check">
		<input class="form-check-input" type="checkbox" name="permissions[]" value=128 {EDIT_CATEGORIES}>
		<label class="form-check-label" for="edit-category">EDIT CATEGORY</label>
	</div>
	<div class="form-check">
  		<input class="form-check-input" type="checkbox" name="permissions[]" value=256 {ADD_USERS}>
  		<label class="form-check-label" for="add-users">ADD USERS</label>
	</div>
	<div class="form-check">
		<input class="form-check-input" type="checkbox" name="permissions[]" value=512 {DELETE_USERS}>
		<label class="form-check-label" for="delete-users">DELETE USERS</label>
	</div>
	<div class="form-check">
  		<input class="form-check-input" type="checkbox" name="permissions[]" value=1024 {EDIT_USERS}>
  		<label class="form-check-label" for="edit-users">EDIT USERS</label>
	</div>
    <button id="sign-up-btn" class="btn btn-lg btn-primary btn-block"  name="edit" type="submit">Edit</button>
    <p class="mt-5 mb-3 text-muted">© 2017-2018</p>
    </form>';

    $search = array("{USER_ID}", "{NAME}", "{EMAIL}", 
    	"{ADD_PRODUCTS}", "{DELETE_PRODUCTS}", "{EDIT_PRODUCTS}", "{DELETE_OWN_PRODUCTS}", 
    	"{EDIT_OWN_PRODUCTS}", "{ADD_CATEGORIES}", "{DELETE_CATEGORIES}", "{EDIT_CATEGORIES}", 
    	"{ADD_USERS}", "{DELETE_USERS}", "{EDIT_USERS}");
    $replace = array($user_id, $user['name'], $user['email'], $add_products, $delete_products, 
    	$edit_products, $delete_own_products, $edit_own_products, $add_categories, 
    	$delete_categories, $edit_categories, $add_users, $delete_users, $edit_users);
    $edit_user_form = str_replace($search, $replace, $edit_user_form);

	echo($edit_user_form);
}



function edit_user($mysqli, $user_id, $info) {
	$update_user_result = update_user($mysqli, $user_id, $info['name'], $info['email'], $info['permissions']);
}

//EDITCHECKOUTS

function display_edit_checkout_form($mysqli, $checkout_id) {
	$select_checkouts_result = select_checkout($mysqli, $checkout_id);
	$checkout = $select_checkouts_result->fetch_array();

    $edit_checkout_form = '<form id="edit_checkout_form" class="edit-product-form" method="post" action="editcheckout.php?checkout_id={CHECKOUT_ID}">
    <h1 class="h3 mb-3 font-weight-normal">Editing Checkout</h1>
    <label for="ship_by">Ship by</label>
    <input type="datetime-local" class="form-control" name="ship_by" autofocus="" value="{SHIP_BY}">
    <label for="address">Address</label>
    <input type="text" class="form-control" name="address" required="" autofocus="" value="{ADDRESS}">
    <label for="postal_code">Postal code</label>
    <input type="text" class="form-control" name="postal_code" required="" autofocus="" value="{POSTAL_CODE}">
    <label for="shipment_status_id">Shipment status</label>
    <select class="form-control" name="shipment_status_id">
    	<option value=0 {PREPARING}>PREPARING SHIPMENT</option>
    	<option value=1 {READY}>READY TO BE SHIPPED</option>
    	<option value=2 {SHIPPING}>SHIPPING</option>
    	<option value=3 {SHIPPED}>SHIPPED</option>
    </select>
    <label for="location">Location</label>
    <input type="text" class="form-control" name="location" required="" autofocus="" value="{LOCATION}">
    <button id="sign-up-btn" class="btn btn-lg btn-primary btn-block"  name="edit_checkout" type="submit">Edit</button>
    <p class="mt-5 mb-3 text-muted">© 2017-2018</p>
    </form>';

    $statuses = array("","","","");
    $statuses[$checkout['shipment_status_id']] = "selected";

    $search = array("{CHECKOUT_ID}", "{SHIP_BY}", "{ADDRESS}", 
    	"{PREPARING}", "{READY}", "{SHIPPING}", "{SHIPPED}", 
    	"{POSTAL_CODE}", "{LOCATION}");
    $ship_by = new DateTime($checkout['ship_by']);
    $replace = array($checkout_id, $ship_by->format('Y-m-d\Th:m:00'), $checkout['address'], 
    	$statuses[0], $statuses[1], $statuses[2], $statuses[3],
    	$checkout['postal_code'], $checkout['location']);
    $edit_checkout_form = str_replace($search, $replace, $edit_checkout_form);

	echo($edit_checkout_form);

}

function edit_checkout($mysqli, $checkout_id, $info) {
	$ship_by_datetime = new DateTime($info['ship_by']);

	$update_checkout_result = update_checkout($mysqli, $ship_by_datetime->format('Y-m-d H:i:s'),
	 									$info['address'], $info['postal_code'], $info['shipment_status_id'], 
	 									$info['location'], $checkout_id);

}
//PERMISSIONS

function has_permissions($user_permissions, $permissions) {
	$permission_check = true;	
	foreach ($permissions as $permission) {
		$permission_check&= $user_permissions && $permission;
	}
	return $permission_check;
}

function owns_product($mysqli, $user_id, $product_id) {
	$select_user_product_result = select_user_product($mysqli, $user_id, $product_id);
	return ($select_user_product_result->num_rows == 1);
}


function can_edit_product($mysqli, $user, $product_id) {
	return (has_permissions($user['permisssions'], array(4)) || 
		(has_permissions($user['permissions'], array(16)) && owns_product($mysqli, $user['user_id'], $product_id)));
}

//RESETPASSWORD

function display_password_reset_form($mysqli, $email, $password_reset_key) {
    $select_password_reset_key_result = select_password_reset_key($mysqli, $email, $password_reset_key);

    if ($select_password_reset_key_result) {

	    $password_reset_form = '<form  class="password-reset-form" method="post" action="resetpassword.php?email={EMAIL}&key={KEY}">
	    <h1 class="h3 mb-3 font-weight-normal">Reset password</h1>
	    <label for="name">Password for {EMAIL}</label>
	    <input type="password" class="form-control" name="password" required="" autofocus="">
	    <button id="sign-up-btn" class="btn btn-lg btn-primary btn-block"  name="reset_password" type="submit">Reset</button>
	    <p class="mt-5 mb-3 text-muted">© 2017-2018</p>
	    </form>';

	    $search = array("{EMAIL}", "{KEY}");
	    $replace = array($email, $password_reset_key);
	    $password_reset_form = str_replace($search, $replace, $password_reset_form);

		echo($password_reset_form);

    }
}


function reset_password($mysqli, $email, $key, $password) {
    $select_password_reset_key_result = select_password_reset_key($mysqli, $email, $key);

    if ($select_password_reset_key_result->num_rows != 0) {
        $password_reset_key = $select_password_reset_key_result->fetch_array();

        $update_user_password_result = update_user_password($mysqli, $password_reset_key['user_id'], md5($password));

        echo($mysqli->error);
        return delete_password_reset_key($mysqli, $password_reset_key['user_id']);
    }
}

//FORGOTPASSWORD

function display_send_password_reset_email_form() {
	$password_reset_form = '<form  class="password-reset-form" method="post" action="forgotpassword.php">
	    <h1 class="h3 mb-3 font-weight-normal">Send Password Reset Email</h1>
	    <label for="name">Account Email</label>
	    <input type="email" class="form-control" name="email" required="" autofocus="">
	    <button id="sign-up-btn" class="btn btn-lg btn-primary btn-block"  name="reset_password" type="submit">Send</button>
	    <p class="mt-5 mb-3 text-muted">© 2017-2018</p>
	    </form>';
	echo($password_reset_form);
}

function send_password_reset_email($mysqli, $email, $gmail_account, $gmail_password) {
	$select_user_result = select_user_by_email($mysqli, $email);
	if ($select_user_result) {
		$user = $select_user_result->fetch_array();

		//Make Confirmation Key
	    $password_reset_key = md5($user['name'].$user['email'].date("Ymdhms"));
	    //Save it to database
	    $insert_password_reset_key_result = insert_password_reset_key($mysqli, $user_id, $password_reset_key);
		$template = file_get_contents("password_reset_email_template.txt");
		send_email($email, $user['name'], $password_reset_key, $gmail_account, $gmail_password, $template, "Reset your Password for Kevin's Store");
	} else {

	}
}
//PRODUCTDETAILS

function display_product_details($mysqli, $product_id, $cart) {
	$select_product_result = select_product($mysqli, $product_id);

	$product = $select_product_result->fetch_array();

	$product_item = '<div class="card my-product-item"> <div class="card-body">
    <h6 class="card-title my-product-name">{NAME}</h6>
    <p class="card-subtitle mb-2 text-muted">${PRICE}</p>';

    if (isset($cart) && isset($cart[$product_id])) {
    	$quantity_in_cart = '<p class="card-subtitle mb-2 text-muted">(';
    	$quantity_in_cart.= $cart[$product_id]['quantity'];
    	$quantity_in_cart.=' in cart)</p>';
    	$product_item.= $quantity_in_cart;
    }

    $product_item.= '<form id="product-details-checkout-form" class="edit-inventory-form" method="post" action="productdetails.php?product_id={PRODUCT_ID}">
    	<div class="product-detail-image-container">
			<img class="product-image" alt="Thumbnail" src="{IMAGE}">
    	</div> 
    	<p class="card-text">Description:</p>
    	<p class="card-text">{DESCRIPTION}</p>
    	<input class="product-quantity-input form-control" type="number" name="quantity" value=0>
    	<button class="product-details-checkout-btn btn btn-lg btn-primary btn-block"  name="add_to_cart" type="submit">Add to Cart</button>
    </form>';

    $search = array("{NAME}", "{PRICE}", "{IMAGE}", "{DESCRIPTION}", "{PRODUCT_ID}");
    $replace = array($product['name'], $product['price'], $product['image'], $product['description'], $product['product_id']);
    $product_item = str_replace($search, $replace, $product_item);
    echo($product_item);
}

function add_quantity_to_session_cart($cart, $product_id, $quantity) {
	if (isset($cart[$product_id])) {
		$cart[$product_id]['quantity'] += $quantity;
	} else {
		$select_product_result = select_product($mysqli, $product_id);
		$select_product_result = $mysqli->query($select_product);
		if ($select_product_result->num_rows != 0) {
			$product = $select_product_result->fetch_array();
			$cart[$product_id] = array("quantity" => $quantity, "price" => $product['price']);
		}
	}
	return $cart;
}


function add_quantity_to_user_cart($mysqli, $user, $product_id, $quantity) {
	$select_cart_product_result = select_cart_product_by_user_product($user['user_id'], $product_id);

	if ($select_cart_product_result->num_rows > 0) {
		$update_cart_product_result = update_cart_product($mysqli, $user['user_id'], $product_id, $quantity);
	} else {
		$insert_cart_product_result = insert_cart_product($mysqli, $user['user_id'], $product_id, $quanity);
	}
}

//PAYPAL 

function handle_transaction_id($transaction_id, $mysqli, $user) {
	$pp_hostname = "www.sandbox.paypal.com";

	$req = 'cmd=_notify-synch';
	$req.= "&tx=";
	$req.= $transaction_id;
	$req.= "&at=";
	$req.= "W9b9px8Yu7geqreMniZAx8a0HAdoQF1uS3ygc06oHfNEjat7-GGVW1JOc2y";
	 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://$pp_hostname/cgi-bin/webscr");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: $pp_hostname"));
	$res = curl_exec($ch);
	curl_close($ch);
	
	if(!$res){
	    //HTTP ERROR
	}else{
	     // parse the data
	    $lines = explode("\n", trim($res));
	    $keyarray = array();
	    if (strcmp ($lines[0], "SUCCESS") == 0) {
	    	$select_user_checkout_result = select_checkouts_by_user_payment_confirmed($mysqli, $user['user_id'], 0);
	    	
	    	$delete_cart_product = "DELETE FROM cart_product WHERE user_id = "; 
	    	$delete_cart_product.= $user['user_id'];

	    	$delete_cart_product_result = $mysqli->query($delete_cart_product);


	        for ($i = 1; $i < count($lines); $i++) {
	            $temp = explode("=", $lines[$i],2);
	            $keyarray[urldecode($temp[0])] = urldecode($temp[1]);
	        }

	    	if ($select_user_checkout_result->num_rows == 1) {
				$checkout = $select_user_checkout_result->fetch_array();

				if ($checkout['total_amount'] == $keyarray['mc_gross']) {
					update_checkout_payment_confirmed($mysqli, $checkout['checkout_id'], 1);
			    return $keyarray;
				}
	    	}
	    }
	    else if (strcmp ($lines[0], "FAIL") == 0) {
	    }
	}
}


function display_complete_checkout_message($key_array) {
    $checkout_message = '<h1>Checkout Successful!</h1>
					    <p class="lead"> Thank you, {FIRSTNAME} {LASTNAME}. </p>
					    <p class="lead"> You spent {AMOUNT} from your sandbox paypal account.</p>';

	$search = array("{FIRSTNAME}", "{LASTNAME}", "{AMOUNT}");
	$replace = array($key_array['first_name'], $key_array['last_name'], $key_array['mc_gross']);
	$checkout_message = str_replace($search, $replace, $checkout_message);
	echo($checkout_message);
}

//COMPLETEPAYMENT

function display_complete_payment_form($checkout_amount) {
	$paypal_form = '
	<form class="make-payment-form" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">
		<h2>You are paying ${CHECKOUT_AMOUNT} from your sandbox paypal account.</h2>
		<p class="lead">Thanks for Shopping.</p>
	    <input type="hidden" name="cmd" value="_xclick">
	    <input type="hidden" name="business" value="kangstore@gmail.com">
	    <input type="hidden" name="lc" value="CA">
	    <input type="hidden" name="button_subtype" value="services">
	    <input type="hidden" name="no_note" value="1">
	    <input type="hidden" name="no_shipping" value="1">
	    <input type="hidden" name="currency_code" value="CAD">
	    <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted">
	    <input type="hidden" name="amount" value="{CHECKOUT_AMOUNT}">
	    <input type="image" src="https://www.sandbox.paypal.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	    <img alt="" border="0" src="https://www.sandbox.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>';

	$paypal_form = str_replace("{CHECKOUT_AMOUNT}", $checkout_amount, $paypal_form);

	echo($paypal_form);
}



//ALL

function check_login_redirect($session, $url) {
    if (!isset($session['user'])) {
    	$redirect = "Location: login.php?login_required=";
    	$redirect.= $url;
        header($redirect);
        die();
    }
}
?>