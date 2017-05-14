<?php
include('php/common.php');

$bookings = getBookings();

checkRemoveBooking();
checkClearBookings();

$conn = dbConnect();

function getFlight($route){
	global $conn, $config;
	$table = $config->table;
    $sql = "SELECT $table->route, $table->from, $table->to, $table->price FROM $table->name WHERE $table->route=$route";
    $result = $conn->query($sql);
    $flights = array();
    if ($result->num_rows == 1) {
        while($row = $result->fetch_assoc()) {
            return new Flight($row[$table->route],$row[$table->from],$row[$table->to],$row[$table->price]);
        }
    }
    return $flights;
}

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
		if(!($bookingIndex === NULL)){
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
	<title></title>
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
		</form>
	</div>
	<div id="snackbarContainer" class="mdl-js-snackbar mdl-snackbar">
		<div class="mdl-snackbar__text"></div>
		<button class="mdl-snackbar__action" type="button"></button>
	</div>
</body>
</html>

<?php mysqli_close($conn); ?>