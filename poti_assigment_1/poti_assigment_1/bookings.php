<?php
include('php/common.php');

$bookings = getBookings();

checkRemoveBooking();
checkClearBookings();

$conn = dbConnect();


function countBookingAttr($booking, $attrName){
	$count = 0;
	foreach($booking->seats AS $seat) {
		foreach ($seat AS $key => $value){
			if($key == $attrName && $value == true) ++$count;
		}
	}
	return $count;
}

function checkRemoveBooking(){
	global $bookings;
	$bookingID = isset($_REQUEST["removeid"]) ? $_REQUEST["removeid"] : null;
	if($bookingID){
		$bookingIndex = $bookings->findBookingIndexViaID($bookingID);
		if($bookingIndex !== NULL){
			array_splice($bookings->bookings, $bookingIndex, 1); 
		}
	}
}

function checkClearBookings(){
	global $bookings;
	$clear = isset($_REQUEST["clear"]) ? $_REQUEST["clear"] : null;
	if($clear){
		$bookings->clearBookings();
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>My Bookings</title>
	<meta charset="utf-8" />
	<script src="scripts/jquery-3.2.1.min.js"></script>
	<link href="styles/material.min.css" rel="stylesheet" />
	<script src="scripts/material.min.js"></script>
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
	<script src="scripts/common.js"></script>
	<style type="text/css">
		.bookingCard{
			width:100%;
		}
		.left{
			text-align:left;
		}
		.right{
			text-align:right;
		}
	</style>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#ChechoutForm").submit(onFormSubmit);
		});
		
		function onFormSubmit(){
			var cbs = $("[id^=booking-checkbox]");
			var checkedCount = 0;
			cbs.each(function(index, item){
				var cb = $(item);
				if(cb.is(':checked'))
					checkedCount++;
			});
			if(checkedCount == 0){
				showMessage("Please select at lest 1 booking to procide to checkout.");
				return false;
			}
			return true;
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
				<span class="mdl-layout-title">My Bookings</span>
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
				<div style="text-align:center;">
					<h1>My Bookings</h1>
					<form style="width:80%;margin:auto;" action="checkout.php" method="post" id="ChechoutForm">
						<div class="mdl-grid">
						<?php foreach($bookings->bookings as $booking) { 
							$flight = getFlight($booking->route);
						?>
							<div class="mdl-cell mdl-cell--4-col">				
								<div class="mdl-card mdl-shadow--2dp bookingCard">
									<div class="mdl-card__title">
										<h2 class="mdl-card__title-text">Flight: <?php print($flight->from . " to " . $flight->to) ?></h2>
									</div>
									<div class="mdl-card__supporting-text left">
										<p>Route Number: <?php print($flight->route) ?></p>
										<p>Details:
											<br/>
											<span>Adults: <?php print(countBookingAttr($booking,"Adult")) ?></span>
											<span>Children: <?php print(countBookingAttr($booking,"Child")) ?></span>
										</p>
									</div>
									<div class="mdl-card__actions mdl-card--border">
										<a class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" href="bookings.php?removeid=<?php print($booking->ID) ?>">
											Remove
										</a>
										<span style="display:inline-block; width:50%;"></span>
										<a class="mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored" href="seatselection.php?bookingid=<?php print($booking->ID) ?>">
											<i class="material-icons">edit</i>
										</a>
									</div>
									<div class="mdl-card__menu">
										<label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="booking-checkbox-<?php print($booking->ID) ?>">
											<input type="checkbox" id="booking-checkbox-<?php print($booking->ID) ?>" class="mdl-checkbox__input" name="selectedBookings[]" value="<?php print($booking->ID) ?>">
										</label>
									</div>
								</div>
							</div>
						<?php } ?>
						</div>
						<div>
							<a class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" href="searchflights.html">Add More Flights</a>
							<span style="display:inline-block; width:20px;"></span>
							<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" type="submit">Proceed Check Out</button>
							<span style="display:inline-block; width:20px;"></span>
							<a class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" href="bookings.php?clear=true">Clear Flights</a>
						</div>
						<br/>
						<p>Please select a booking to continue to checkout</p>
					</form>
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
</body>
</html>

<?php mysqli_close($conn); ?>