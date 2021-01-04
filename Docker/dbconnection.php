<?php

$server="localhost";
$username="root";
$password="09Dylan04";
$database="shippingchallenge";

echo "<h1>Shipping challenge page</h1> <br/>";

/*database connection*/
$connect=new mysqli($server, $username, $password, $database);

/*webpagina vullen*/
if($connect->connect_error)
   echo "Connection error:" .$connect->connect_error;
else
   echo "Connection succesful <br/>";
   

?>
