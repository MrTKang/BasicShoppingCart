<?php
require("includes/connection.php");
session_start();
require("includes/credentials.php");
require_once("vendor/autoload.php");

$redirect_to_login = FALSE;
if (isset($_POST['submit']) && $_POST['password'] == $_POST['passwordagain']){
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
        $transport->setUsername($GMAIL_ACCOUNT);
        $transport->setPassword($GMAIL_PASSWORD);
        $mailer = new Swift_Mailer($transport);

        $email_message = new Swift_Message("Welcome to Kevin's Store");
        $email_message->setFrom(['freestore0202@gmail.com' => "Kevin's Store"]);
        $email_message->setTo([$_POST['email'] => $_POST['name']]);
        $email_message->setBody($template, 'text/html');

        $send_result = $mailer->send($email_message);

        $message = "Please check your email";
    }


} else if (isset($_POST['submit']) && $_POST['password'] != $_POST['passwordagain']) {
    $error_message = "passwords are not matching";
}

if ($redirect_to_login) {
     header("Location: login.php");
 }

?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Sign Up</title>
        <link href="css/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
    </head>

    <body>
        <div class="signup-container">
            <form class="signup-form" method="post" action="signup.php">
                <h1 class="h3 mb-3 font-weight-normal">Please sign up</h1>
                <h6> <?php echo $template ?></h6>
                <h6> <?php echo $error_message ?></h6>
                <label for="name">Your Name</label>
                <input type="text" class="form-control" name="name" required="" autofocus="">
                <label for="email">Email address</label>
                <input type="email" class="form-control" name="email" required="" autofocus="">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" required="">
                <label for="passwordagain">Password Again</label>
                <input type="password" class="form-control" name="passwordagain" required="">
                <button id="sign-up-btn" class="btn btn-lg btn-primary btn-block"  name="submit" type="submit">Sign up</button>
                <p class="mt-5 mb-3 text-muted">© 2017-2018</p>
            </form>
        </div>
    </body>
</html>