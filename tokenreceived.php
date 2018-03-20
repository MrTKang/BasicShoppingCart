<?php
require("includes/connection.php");

$insert_token = "INSERT INTO token (token) VALUES ('";
$insert_token.= "aaaaaa";
$insert_token.= "')";
$insert_token_result = $mysqli->query($insert_token);
?>