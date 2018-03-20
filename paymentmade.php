<?php 

$req = 'cmd=_notify-synch';
$tx_token = $_GET['tx'];
$auth_token = "W9b9px8Yu7geqreMniZAx8a0HAdoQF1uS3ygc06oHfNEjat7-GGVW1JOc2y";
$req .= "&tx=$tx_token&at=$auth_token";
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.sandbox.paypal.com/cgi-bin/webscr");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
//set cacert.pem verisign certificate path in curl using 'CURLOPT_CAINFO' field here,
//if your server does not bundled with default verisign certificates.
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: www.sandbox.paypal.com"));
$res = curl_exec($ch);
curl_close($ch);



if(!$res){
    //HTTP ERROR
}else{
     // parse the data
    $lines = explode("\n", trim($res));
    $keyarray = array();
    if (strcmp ($lines[0], "SUCCESS") == 0) {
        for ($i = 1; $i < count($lines); $i++) {
            $temp = explode("=", $lines[$i],2);
            $keyarray[urldecode($temp[0])] = urldecode($temp[1]);
        }
    // check the payment_status is Completed
    // check that txn_id has not been previously processed
    // check that receiver_email is your Primary PayPal email
    // check that payment_amount/payment_currency are correct
    // process payment
    $firstname = $keyarray['first_name'];
    $lastname = $keyarray['last_name'];
    $itemname = $keyarray['item_name'];
    $amount = $keyarray['payment_gross'];
     
    echo ("<p><h3>Thank you for your purchase!</h3></p>");
     
    echo ("<b>Payment Details</b><br>\n");
    echo ("<li>Name: $firstname $lastname</li>\n");
    echo ("<li>Item: $itemname</li>\n");
    echo ("<li>Amount: $amount</li>\n");
    echo ("");
    }
    else if (strcmp ($lines[0], "FAIL") == 0) {
        // log for manual investigation
    }
}
?>