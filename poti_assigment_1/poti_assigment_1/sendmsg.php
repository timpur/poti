<?php

sendConfirmEmail();
sendCustomerEmail();


function sendConfirmEmail(){
	$name = $_REQUEST["firstname"] . " " . $_REQUEST["lastname"];
	$to = $_REQUEST["email"];
	$subject = "We have recived your Message";
	$message = generateHTMLMSG($name);
	$headers = 'From: noreply@uts.edu.com';
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

	mail($to, $subject, $message, $headers);
}

function sendCustomerEmail(){
	$name = $_REQUEST["firstname"] . " " . $_REQUEST["lastname"];
	$to = "timothy@purchas.com";
	$subject = 'Customer Subject: ' . $_REQUEST["subject"];
	$message = '
	Customer Name: ' . $name. '
	Customer Email: ' . $_REQUEST["email"] . '
	Customer Message: 
	' . $_REQUEST["message"] . '
	';
	$headers = 'From: noreply@uts.edu.com';

	mail($to, $subject, $message, $headers);
}

function generateHTMLMSG($name){
	$html = '
			<p>Dear, ' . $name . '</p>
			<p>We have recived your message and will get back to you shortly</p>
			';
	return $html;
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
				<span class="mdl-layout-title">Success</span>
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
				<h1>You Have Successful Sent Us an Email<h1>
				<h3>The email is on its say<h3>
				<h4>We will get back to you soon.<h4>
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

