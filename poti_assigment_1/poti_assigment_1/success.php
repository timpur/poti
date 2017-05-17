<?php 
include('php/common.php');
$conn = dbConnect();

$bookings = getBookings();
$success = NULL;
$currentBookings = getCurrentBookings();
sendEmail();
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
	$subject = 'Successful Booked';
	$message = generateHTMLMSG();
	$headers = 'From: noreply@uts.edu.com';
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

	mail($to, $subject, $message, $headers);
}

function generateHTMLMSG(){
	global $currentBookings;
	$html = '
			<p><b>You Have Successful Booked. Here is a summary of your Booking</b></p>
			<br />
			<br />
			<table>
				<thead>
					<tr>
						<th>Item</th>
						<th>Quantity</th>
						<th>Price</th>
						<th>Total</th>
					</tr>
				</thead>
				<tbody>';
	$total = 0;
	foreach($currentBookings as $booking){
		global $total;
		$flight = getFlight($booking->route);
		$seatCount = count($booking->seats);
		$price = $flight->price;
		$bookingTotal = $price * $seatCount;		
		$total += $bookingTotal;
		$html .= '<tr>
					<td>Flight: ' . $flight->from . " to " . $flight->to . ' </td>
					<td> ' . $seatCount . ' </td>
					<td> $' . $price . ' </td>
					<td> $' . $bookingTotal . ' </td>
				</tr>';
	}
	$html .= '<tr>
				<td>Total</td>
				<td></td>
				<td></td>
				<td>$' . $total . '</td>
			</tr>
		</tbody>
	</table>';
	return $html;
}

function getCurrentBookings(){
	global $bookings;
	if($_REQUEST["bookingid"]){
		$currentBookings = array();
		foreach($_REQUEST["bookingid"] as $bookingID){
			array_push($currentBookings, $bookings->findBookingViaID($bookingID));
		}
		return $currentBookings;
	}
	return NULL;
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
				<h3>An email is on its way<h3>
				<P>You can use the navigation to, go to the Home, Search or My Bookings, to contine with booking more flights </P>
			</div>
		</main>
		<footer class="mdl-mini-footer" style="padding:20px 16px;">
			<div class="mdl-mini-footer__left-section">
				<div class="mdl-logo">More About Us</div>
				<ul class="mdl-mini-footer__link-list">
				  <li><a href="contactus.html">Contact Us</a></li>
				</ul>
			</div>
		</footer>
	</div>
</body>
</html>