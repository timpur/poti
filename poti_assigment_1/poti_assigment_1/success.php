<?php 
include('php/common.php');

$bookings = getBookings();
$success = NULL;
//print_r($_REQUEST);

removeBookings();


function removeBookings(){
	global $bookings, $success;
	if($_REQUEST["bookingid"]){
		foreach($_REQUEST["bookingid"] as $id)
			$bookings->removeBooking($id);
		$success = true;
	}
	else {
		$success = false;
	}
}

function sendEmail(){
	$to = $_REQUEST["email"];
	$subject = 'Successful Booking';
	$message = 'You Have Successful Booked for the Following Bookings';
	$headers = 'From: webmaster@example.com';

mail($to, $subject, $message, $headers);
}

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
	<script src="scripts/common.js"></script>
    <title>Booking Success</title>
    <meta charset="utf-8" />
</head>
<body>
	<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
		<header class="mdl-layout__header">
			<div class="mdl-layout__header-row">
				<span class="mdl-layout-title">Booking Success</span>
			</div>
		</header>
		<div class="mdl-layout__drawer">
			<span class="mdl-layout-title">Navigation</span>
			<nav class="mdl-navigation">
				<a class="mdl-navigation__link" href="index.html">Home</a>
				<a class="mdl-navigation__link" href="searchflights.html">Search For Flights</a>
				<a class="mdl-navigation__link" href="bookings.php">My Bookings</a>
			</nav>
		</div>
		<main class="mdl-layout__content">
			<div class="page-content" style="text-align:center; margin-top:30vh;">
				<h1>You Have Successful Booked<h1>
				<h3>An email is on its Way<h3>
			</div>
		</main>
		<footer class="mdl-mini-footer" style="padding:20px 16px;">
			<div class="mdl-mini-footer__left-section">
				<div class="mdl-logo">More About Us</div>
				<ul class="mdl-mini-footer__link-list">
				  <li><a href="contactus.php">Contact Us</a></li>
				</ul>
			</div>
		</footer>
	</div>
</body>
</html>