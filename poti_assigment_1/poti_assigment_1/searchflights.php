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
    $sql = "SELECT $table->route, $table->from, $table->to, $table->price FROM $table->name";
    $result = $conn->query($sql);

    $flights = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $flight = new Flight($row[$table->route],$row[$table->from],$row[$table->to],$row[$table->price]);
            array_push($flights,$flight);
        }
    }
    return $flights;
}

?>


<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="utf-8" />

    <script src="scripts/jquery-3.2.1.min.js"></script>
    <script src="scripts/angular.min.js"></script>

    <style type="text/css"></style>
    <script type="text/javascript">

        var app = angular.module('app', []);
        app.controller('flights', function ($scope) {
            $scope.flights = <?php print json_encode($flights); ?>;
            $scope.next = function () {
                alert('next');
            }
        });

    </script>
</head>
<body ng-app="app" ng-controller="flights" style="text-align:center;">
    <h1>Search for Flights</h1>
    <table style="margin:auto;" border="1">
        <thead></thead>
        <tbody>
            <tr ng-repeat="flight in flights">
                <td>{{flight.rout}}</td>
                <td>{{flight.from}}</td>
                <td>{{flight.to}}</td>
                <td>{{flight.price}}</td>
                <td>{{flight.selected}}</td>
                <td>
                    <input type="checkbox" ng-model="flight.selected" />
                </td>
            </tr>
        </tbody>
    </table>
    <br />
    <button ng-click="next()">Next</button>
</body>
</html>
