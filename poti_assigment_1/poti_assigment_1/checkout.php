<?php 
include('php/common.php');

$bookings = getBookings();
$currentBookings = getCurrentBookings();

if(count($currentBookings) == 0) die("Error: No booking IDs found");

//print(json_encode($currentBookings));

$conn = dbConnect();

$total = 0;

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


<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8" />
	<script src="scripts/jquery-3.2.1.min.js"></script>
	<link href="styles/material.min.css" rel="stylesheet" />
	<link href="styles/mdl-selectfield.min.css" rel="stylesheet" />
	<script src="scripts/material.min.js"></script>
	<script src="scripts/mdl-selectfield.min.js"></script>
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
	<script src="scripts/common.js"></script>
	<style type="text/css">
		.half{
			width:150px;
		}
	</style>
	<script type="text/javascript">
		$(document).ready(function(){
			//$("#country").blur(onCountryChange);
		});
	
		function onCountryChange(el){
			var selected = $(el).val();
			var items = $("#state,#zip");
			if(selected == "aus"){
				items.prop('required',true).change();
			}
			else {
				items.prop('required',false);
			}
			items.each(function(index,item){
				item.dispatchEvent(new Event("reset"));
			});
		}
		
		function next(nextTab){
			var form = $("#paymentform");
			var formTab = $(form.find(".mdl-tabs__panel")[nextTab - 1]);
			if(formValid(formTab)){
				var item = form.find(".mdl-tabs__tab")[nextTab];
				item.dispatchEvent(new Event("click"));
			}
			else{
				showMessage("Plese fill out this page correctly.");
			}
		}
		
		function SubmitForm(){
			var form = $("#paymentform");
			if(formValid(form)){
				form.submit();
			}
			else{
				showMessage("Plese fill out the form correctly.");
			}
		}
		
		function formValid(form){
			var formItems = form.find("input, select");
			var valid = true;
			formItems.each(function(index, item){
				if(!item.checkValidity()) valid = false;
			});
			return valid;
		}
		
		function populateReview(){
			var name, email, address;
			var form = $("#paymentform");
			name = form.find("#firstname, #lastname").map(function() {
				return $( this ).val();
			}).get().join( " ");
			email = form.find("#email").val();
			address = jQuery.merge(form.find("#address2"), form.find("#address1, #state, #zip, #country")).map(function() {
				var item = $(this);
				var val = null;
				if(item.is("select"))
					val = $(this.options[this.selectedIndex]).text();
				else
					val = $(this).val();
				
				if(val)
					return val;				
			}).get().join( " ");
			
			var reviewText = form.find("#reviewtext")
			reviewText.find("#name").text(name);
			reviewText.find("#email").text(email);
			reviewText.find("#address").text(address);
		}
		
		function showMessage(message){
			var data = { message: message };
			var snackbarContainer = $("#snackbarContainer")[0];
			snackbarContainer.MaterialSnackbar.showSnackbar(data);
		}
		
	</script>
</head>
<body>
	<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
		<header class="mdl-layout__header">
			<div class="mdl-layout__header-row">
				<span class="mdl-layout-title">Checkout</span>
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
			<div class="page-content">
				<div style="text-align:center; display:table; margin:auto;">
					<h1>Checkout</h1>
					<form id="paymentform" action="success.php" method="post">
						<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">
							<div class="mdl-tabs__tab-bar">
								<a href="#review-bookings-panel" class="mdl-tabs__tab is-active">Review Bookings</a>
								<a href="#personal-details-panel" class="mdl-tabs__tab">Personal Details</a>
								<a href="#payment-details-panel" class="mdl-tabs__tab">Payment Details</a>
								<a href="#confirm-payment-panel" class="mdl-tabs__tab" onclick="populateReview()">Confirm Payment</a>
							</div>
							<div class="mdl-tabs__panel is-active" id="review-bookings-panel">
								<h3>Review Bookings</h3>
								<table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp" style="margin:auto; width:auto">
									<thead>
										<tr>
											<th class="mdl-data-table__cell--non-numeric">Item</th>
											<th class="mdl-data-table__cell--non-numeric">Quantity</th>
											<th class="mdl-data-table__cell--non-numeric">Price</th>
											<th class="mdl-data-table__cell--non-numeric">Total</th>
										</tr>
									</thead>
									<tbody>
										<?php 
											$total = 0;
											foreach($currentBookings as $booking) { 
												$flight = getFlight($booking->route);
												$seatCount = count($booking->seats);
												$price = $flight->price;
												$bookingTotal = $price * $seatCount;
												global $total;
												$total += $bookingTotal;
										?>
										<tr>
											<td class="mdl-data-table__cell--non-numeric">Flight: <?php print($flight->from . " to " . $flight->to) ?></td>
											<td><?php print($seatCount) ?></td>
											<td>$<?php print($price) ?></td>
											<td>$<?php print($bookingTotal) ?></td>
										</tr>
										<?php } ?>
										<tr>
											<td class="mdl-data-table__cell--non-numeric">Total</td>
											<td></td>
											<td></td>
											<td>$<?php print($total) ?></td>
										</tr>
									</tbody>
								</table>
								<br/>
								<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="next(1)" type="button">Next</button>
							</div>
							<div class="mdl-tabs__panel" id="personal-details-panel">
								<h3>Personal Details</h3>
								<div>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="text" id="firstname" name="firstName" required>
										<label class="mdl-textfield__label" for="firstname">First Name</label>
									</div>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="text" id="lastname" name="lastName" required>
										<label class="mdl-textfield__label" for="lastName">Last Name</label>
									</div>
									<br/>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="text" id="mobile" name="mobile" pattern="[0-9]{10}" required>
										<label class="mdl-textfield__label" for="mobile">Mobile Number</label>
										<span class="mdl-textfield__error">Not a valid Mobile Number (10)</span>
									</div>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="email" id="email" name="email" required>
										<label class="mdl-textfield__label" for="email">Email</label>
										<span class="mdl-textfield__error">Not a valid Email Address</span>
									</div>
									<br/>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="text" id="address1" name="address1" required>
										<label class="mdl-textfield__label" for="address1">Address 1</label>
									</div>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="text" id="address2" name="address2">
										<label class="mdl-textfield__label" for="address2">Address 2</label>
									</div>
									<br/>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label half">
										<input class="mdl-textfield__input" type="text" id="state" name="state" required>
										<label class="mdl-textfield__label" for="state">State</label>
									</div>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label half">
										<input class="mdl-textfield__input" type="text" id="zip" name="zip" pattern="[0-9]{4}" required>
										<label class="mdl-textfield__label" for="zip">Post Code</label>
										<span class="mdl-textfield__error">Not a valid Post Code (4)</span>
									</div>
									<div class="mdl-selectfield mdl-js-selectfield mdl-selectfield--floating-label">
										<select class="mdl-selectfield__select" id="country" name="country" required onchange="onCountryChange(this)">
										  <option value=""></option>
										  <option value="aus">Australia</option>
										  <option value="usa">USA</option>
										  <option value="usa">Other</option>
										</select>
										<label class="mdl-selectfield__label" for="country">Country</label>
									 </div>
									<br/>
									 <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="text" id="workphone" name="workphone" pattern="[0-9]{8}">
										<label class="mdl-textfield__label" for="workphone">Work Number</label>
										<span class="mdl-textfield__error">Not a valid Phone Number (8)</span>
									</div>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="text" id="businessphone" name="businessphone" pattern="[0-9]{8}">
										<label class="mdl-textfield__label" for="businessphone">Business Number</label>
										<span class="mdl-textfield__error">Not a valid Phone Number (8)</span>
									</div>
								</div>	
								<br/>
								<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="next(2)" type="button">Next</button>
							</div>
							<div class="mdl-tabs__panel" id="payment-details-panel">
								<h3>Payment Details</h3>
								<div>
									<div class="mdl-selectfield mdl-js-selectfield mdl-selectfield--floating-label">
										<select class="mdl-selectfield__select" id="cardtype" name="cardtype" required>
										  <option value=""></option>
										  <option value="visa">Visa</option>
										  <option value="diners">Diners</option>
										  <option value="mastercard">Master Card</option>
										  <option value="amex">Amex</option>
										</select>
										<label class="mdl-selectfield__label" for="cardtype">Card Type</label>
									 </div>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="text" id="cardname" name="cardname" required>
										<label class="mdl-textfield__label" for="cardname">Name on Card</label>
									</div>
									<br/>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
										<input class="mdl-textfield__input" type="text" id="cardnumber" name="cardnumber" pattern="[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}" required>
										<label class="mdl-textfield__label" for="cardnumber">Card Number</label>
										<span class="mdl-textfield__error">Card Number format: ####-####-####-####</span>
									</div>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label half">
										<input class="mdl-textfield__input" type="text" id="cardsecurtycode" name="cardsecurtycode" pattern="[0-9]{3}" required>
										<label class="mdl-textfield__label" for="cardsecurtycode">Security Code</label>
										<span class="mdl-textfield__error">Not a valid Security Code (3)</span>
									</div>
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label half">
										<input class="mdl-textfield__input" type="text" id="exdate" name="exdate" required pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}">
										<label class="mdl-textfield__label" for="exdate">Exspiry Date</label>
										<span class="mdl-textfield__error">Date format: yyyy-mm-dd</span>
									</div>
								</div>
								<br/>
								<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="next(3)" type="button">Next</button>
							</div>
							<div class="mdl-tabs__panel" id="confirm-payment-panel">
								<h3>Confirm Payment</h3>
								<table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp" style="margin:auto; width:auto">
									<thead>
										<tr>
											<th class="mdl-data-table__cell--non-numeric">Item</th>
											<th class="mdl-data-table__cell--non-numeric">Quantity</th>
											<th class="mdl-data-table__cell--non-numeric">Price</th>
											<th class="mdl-data-table__cell--non-numeric">Total</th>
										</tr>
									</thead>
									<tbody>
										<?php 
											$total = 0;
											foreach($currentBookings as $booking) { 
												$flight = getFlight($booking->route);
												$seatCount = count($booking->seats);
												$price = $flight->price;
												$bookingTotal = $price * $seatCount;
												global $total;
												$total += $bookingTotal;
										?>
										<tr>
											<td class="mdl-data-table__cell--non-numeric">Flight: <?php print($flight->from . " to " . $flight->to) ?></td>
											<td><?php print($seatCount) ?></td>
											<td>$<?php print($price) ?></td>
											<td>$<?php print($bookingTotal) ?></td>
										</tr>
										<?php } ?>
										<tr>
											<td class="mdl-data-table__cell--non-numeric">Total</td>
											<td></td>
											<td></td>
											<td>$<?php print($total) ?></td>
										</tr>
									</tbody>
								</table>
								<?php foreach($currentBookings as $booking) { ?>
								<input type="text" name="bookingid[]" value="<?php print($booking->ID)?>" hidden/>
								<?php } ?>
								<div id="reviewtext" style="text-align:left; display:inline-block;">
									<h5>Name: <span id="name"></span></h5>
									<h5>Email: <span id="email"></span></h5>
									<h5>Address: <span id="address"></span></h5>
									<h5 css="primary">Please review these details</h5>
									<br/>
								</div>	
								<br/>
								<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" type="button" onclick="SubmitForm()">Confirm Payment</button>
								<br/>
							</div>
						</div>
					</form>
					<br/>
					<br/>
				</div>
				<div id="snackbarContainer" class="mdl-js-snackbar mdl-snackbar mdl-color--accent">
					<div class="mdl-snackbar__text"></div>
					<button class="mdl-snackbar__action" type="button"></button>
				</div>
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
</body>]
</html>

<?php mysqli_close($conn); ?>