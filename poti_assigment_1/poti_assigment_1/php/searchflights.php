<?php
include('common.php');

$data = json_decode(file_get_contents('php://input'), true);

$conn = dbConnect($config);

$flights = getFlights($conn, $config->table, $data);

print(json_encode($flights));

function dbConnect($config){
    $conn = new mysqli($config->host, $config->username, $config->password, $config->database);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
    return $conn;
}

function getFlights($conn, $table, $data){
    // Get Params set if exist else set to null;
    $from = isset($data["from"]) ? $data["from"] : null;
    $to = isset($data["to"]) ? $data["to"]: null;

    // Base Query
    $sql = "SELECT $table->route, $table->from, $table->to, $table->price FROM $table->name";
    // WHERE clause (Search Params)
    if($from || $to){
        $sql .= " WHERE ";
        if($from) $sql .= "$table->from = '$from'";
        if($from && $to) $sql .= " AND ";
        if($to) $sql .= "$table->to = '$to'";
    }

    $result = $conn->query($sql);

    $flights = array();
    // if rows add to flights array
    if ($result->num_rows > 0) {
        // Loop though rows
        while($row = $result->fetch_assoc()) {
            // Add new flight item to flights array
            $flight = new Flight($row[$table->route],$row[$table->from],$row[$table->to],$row[$table->price]);
            array_push($flights,$flight);
        }
    }
    return $flights;
}

mysqli_close($conn);
?>
