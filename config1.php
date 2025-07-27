<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "kasir";

$dbconnect = new mysqli ("$host", "$username", "$password", "$database");

if($dbconnect-> connect_error)
{
    echo "Koneksi gagal -> ".$dbconnect->connect_error;
}
?>


