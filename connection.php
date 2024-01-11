<?php
$host='localhost';
$username='root';
$password='';

$dbname='login';

$con=mysqli_connect($host,$username,$password);
$selectDb=mysqli_select_db($con,$dbname);

?>