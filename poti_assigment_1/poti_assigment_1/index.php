<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);


$config = include('config.php');

//sqli connection, referrencing objects from config.php
$conn = new mysqli($config->host, $config->username, $config->password, $config->database);

if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

printFlights($conn, $config->table);


function printFlights($conn, $table){
    $sql = "SELECT $table->route, $table->from, $table->to, $table->price FROM $table->name";
    print $sql . "<br>";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            print "rout_no: " . $row[$table->route] . " from: " . $row[$table->from] . " to:" . $row[$table->to] . " price:" . $row[$table->price] . "<br>";
        }
    } else {
        echo "0 results";
    }
}

?>


<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="utf-8" />
</head>
<body>
        <h1 align="center">Flight Bookings</h1>
    <footer>
        <p align="center">DISCLAMER: This website was created for educational purposes as an assignment for the Programming on the Internet subject at the University of Technology Sydney.</p>
        <table align="center">
            <button>Search Flights</button>
        </table>
        <p align="center">Developed by Students:</p>
        <p align="center">Tim Purchas</p>
        <p align="center">Carl Matheson 12551848</p>
    </footer>
    <?php
        
    ?>
</body>
</html>
