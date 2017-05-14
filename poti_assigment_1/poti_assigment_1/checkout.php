<?php 
include('php/common.php');

$bookings = getBookings();
$currentBookings = getCurrentBookings();

if(count($currentBookings) == 0) die("Error: No booking IDs found");

//print(json_encode($currentBookings));

function getCurrentBookings(){
	global $bookings;
	$bookingIDs = isset($_REQUEST["selectedBookings"]) ? $_REQUEST["selectedBookings"] : null;
	$currentBookings = array();
	foreach($bookingIDs as $bookingID){
		array_push($currentBookings, $bookings->findBookingViaID($bookingID));
	}
	return $currentBookings;
}


?>



<html>
<head>
	<title></title>
	<meta charset="utf-8" />
	<script src="scripts/jquery-3.2.1.min.js"></script>
	<link href="styles/material.min.css" rel="stylesheet" />
	<script src="scripts/material.min.js"></script>
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
	<script src="scripts/common.js"></script>
	<style type="text/css">
		.half{
			width:40%;
		}
	</style>
	<script type="text/javascript">
	</script>
<head>
<body>
	<div style="text-align:center; display:table; margin:auto;">
		<h1>Checkout</h1>
		<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
			<div class="mdl-tabs__tab-bar">
				<a href="#personal-details-panel" class="mdl-tabs__tab is-active">Personal Details</a>
				<a href="#payment-details-panel" class="mdl-tabs__tab">Payment Details</a>
				<a href="#review-bookings-panel" class="mdl-tabs__tab">Review Bookings</a>
				<a href="#conform-payment-panel" class="mdl-tabs__tab">Confirm Payment</a>
			</div>
			<div class="mdl-tabs__panel is-active" id="personal-details-panel">
				<h3>Personal Details</h3>
				<div class="mdl-grid">
					<div class="mdl-cell mdl-cell--6-col">
						<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
							<input class="mdl-textfield__input" type="text" id="firstname" name="firstName">
							<label class="mdl-textfield__label" for="firstname">First Name</label>
						</div>
						<br/>
						<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
							<input class="mdl-textfield__input" type="text" id="phone" name="phone">
							<label class="mdl-textfield__label" for="phone">Phone Number</label>
						</div>
						<br/>
						<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
							<input class="mdl-textfield__input" type="text" id="address" name="address">
							<label class="mdl-textfield__label" for="address">Address</label>
						</div>
					</div>
					<div class="mdl-cell mdl-cell--6-col">
						<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
							<input class="mdl-textfield__input" type="text" id="lastname" name="lastName">
							<label class="mdl-textfield__label" for="lastName">Last Name</label>
						</div>
						<br/>
						<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
							<input class="mdl-textfield__input" type="text" id="email" name="email">
							<label class="mdl-textfield__label" for="email">Email</label>
						</div>
						<br/>
						<div>
							<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label half">
								<input class="mdl-textfield__input" type="text" id="state" name="state">
								<label class="mdl-textfield__label" for="state">State</label>
							</div>
							<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label half">
								<input class="mdl-textfield__input" type="number" id="zip" name="zip">
								<label class="mdl-textfield__label" for="zip">Post Code</label>
							</div>
						</div>
					</div>
				</div>	
			</div>
			<div class="mdl-tabs__panel" id="payment-details-panel">
				<h3>Payment Details</h3>
			</div>
			<div class="mdl-tabs__panel" id="review-bookings-panel">
				<h3>Review Bookings</h3>
			</div>
			<div class="mdl-tabs__panel" id="conform-payment-panel">
				<h3>Confirm Payment</h3>
			</div>
		</div>
	</div>
</body>
</html>