<?php
include('common.php');

//sqli connection, referrencing objects from config.php

$conn = dbConnect($config);

$flights = getFlights($conn, $config->table);


function dbConnect($config){
    $conn = new mysqli($config->host, $config->username, $config->password, $config->database);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
    return $conn;
}

function getFlights($conn, $table){
    // Get Params set if exist else set to null;
    $from = isset($_GET["from"]) ? $_GET["from"] : null;
    $to = isset($_GET["to"]) ? $_GET["to"]: null;

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

function generateTableRowsForFlights($flights){
    $html = "";
    foreach($flights as $flight){
        $html .= generateTableRowForFlight($flight);
    }
    return $html;
}

function generateTableRowForFlight($flight){
    $html = "<tr>
                <td>$flight->from</td>
                <td>$flight->to</td>
                <td>
                    <input type=\"radio\" name=\"select\" value=\"$flight->route\"/>
                </td>
            </tr>";
    return $html;
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="utf-8" />
    <script src="scripts/jquery-3.2.1.min.js"></script>
    <link href="styles/material.min.css" rel="stylesheet" />
    <script src="scripts/material.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <style type="text/css"></style>
    <script type="text/javascript"></script>
</head>
<body style="text-align:center;">
    <h1>Search for Flights</h1>
    <form method="get" action="">
        <h3>Search</h3>
        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
            <input class="mdl-textfield__input" type="text" id="searchFrom" name="from" />
            <label class="mdl-textfield__label" for="searchFrom">From Location</label>
        </div>
        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
            <input class="mdl-textfield__input" type="text" id="searchTo" name="to" />
            <label class="mdl-textfield__label" for="searchTo">To Location</label>
        </div>
        <button class="mdl-button mdl-js-button mdl-button--fab mdl-button--colored" type="submit">
            <i class="material-icons">search</i>
        </button>
    </form>
    <form method="post" action="">
        <h3>Flight Results</h3>
        <?php if(count($flights) > 0){ ?>
        <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp" style="margin:auto; width:auto">
            <thead>
                <tr>
                    <th class="mdl-data-table__cell--non-numeric">From</th>
                    <th class="mdl-data-table__cell--non-numeric">To</th>
                    <th class="mdl-data-table__cell--non-numeric">Select</th>
                </tr>
            </thead>
            <tbody>
                <?php print generateTableRowsForFlights($flights); ?>
            </tbody>
        </table>
        <br />
        <button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored">Next</button>
        <?php } else { ?>
        <p>No Results Foud</p>
        <?php } ?>
    </form>
</body>
</html>
