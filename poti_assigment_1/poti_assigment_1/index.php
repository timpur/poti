<?php

$config = include('config.php');

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
    <?php
        
    ?>
</body>
</html>
