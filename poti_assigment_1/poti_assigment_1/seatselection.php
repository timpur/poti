<?php
include('php/common.php');

$bookings = getBookings();
//$bookings->clearBookings();
$selectedFlightNo = isset($_REQUEST["selection"]) ? $_REQUEST["selection"] : NULL;
$currentBooking = getCurrentBooking($bookings);

if($currentBooking === NULL && $selectedFlightNo === NULL){
	die("Error: No vailid booking ID was given");
}


function getCurrentBooking($bookings){
	$bookingID = isset($_REQUEST["bookingid"]) ? $_REQUEST["bookingid"] : NULL;
	if($bookingID !== NULL){
		return $bookings->findBookingViaID($bookingID);
	}
	return NULL;
}

function getCurrentBookingID(){
	global $currentBooking, $selectedFlightNo;
	if($currentBooking !== NULL)
		return $currentBooking->ID;
	else
		return -1;
}
function getCurrentBookingRoute(){
	global $currentBooking, $selectedFlightNo;
	if($currentBooking !== NULL)
		return $currentBooking->route;
	else
		return $selectedFlightNo;
}
function getCurrentBookingSeats(){
	global $currentBooking, $selectedFlightNo;
	if($currentBooking !== NULL)
		return $currentBooking->seats;
	else
		return array();
}



?>

<!DOCTYPE html>
<html>
<head>
	<title>Seat Selection</title>
	<meta charset="utf-8" />
	<script src="scripts/jquery-3.2.1.min.js"></script>
	<link href="styles/material.min.css" rel="stylesheet" />
	<script src="scripts/material.min.js"></script>
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
	<script src="scripts/common.js"></script>
	<script type="text/javascript">
		var bookingID = <?php print(getCurrentBookingID()); ?>;
		var route = <?php print(getCurrentBookingRoute()); ?>;
		var seats = <?php print(json_encode(getCurrentBookingSeats())); ?>;
		
		$(document).ready(function(){
			loadSeats();
		});
		
		function loadSeats(){
			var rows = generateSeats();
			$("#SeatsTableBody").empty().append(rows);
			upDateSelectedSeatsCheckbox();
			upDateSelectedSeatsList();
		}
		
		function upDateSelectedSeatsCheckbox(){
			for(var i = 0; i < seats.length; ++i){
				var seat = seats[i];
				var cb = $("#checkbox-" + seat.row + "-" + seat.col);
				cb.prop('checked',true);
				cb.parent().addClass("is-checked");
			}
		}
		
		function upDateSelectedSeatsList(){
			var rows = generateSeatOptions();
			$("#OptionsTableBody").empty().append(rows);
		}
		
		function seatSelectedChanged(cb, row, col){
			cb = $(cb);
			if(cb.is(':checked')){
				seats.push({
					"row":row,
					"col":col
				});
			}
			else{
				var seat = findSeat(row,col)
				if(seat != -1){
					seats.splice(seat,1);
				}
			}
			upDateSelectedSeatsList();
		}
		
		function seatSelectedOptionChanged(cb, row, col, option){
			cb = $(cb);
			var index = findSeat(row,col);
			if(index != -1){
				var seat = seats[index];
				if(cb.is(':checked'))
					seat[option] = true;
				else 
					seat[option] = false;
			}
		}

		function findSeat(row, col){
			for(var i = 0; i < seats.length; i++){
				if(seats[i].row == row && seats[i].col == col)
					return i
			}
			return -1;
		}
		
		function generateSeats(){
			var rows = $([]);
			for(var row = 1; row <= 5; ++row){
				rows = rows.add(generateSeat(row));
			}
			return rows;
		}
		
		function generateSeat(rowNum){
			var row = $('<tr>');
			row.append($('<td>').text(rowNum));
			for(var col = 1; col <= 5; ++col){
				row.append(generateSeatCB(rowNum,col));
			}
			return row;
		}
		
		function generateSeatCB(row, col){
			var cell = $('<td class="mdl-data-table__cell--non-numeric">');
			var label = $('<label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="checkbox-'+row+'-'+col+'">');
			var cb = $('<input type="checkbox" id="checkbox-'+row+'-'+col+'" class="mdl-checkbox__input" onclick="seatSelectedChanged(this, '+row+', '+col+')"/>')
			label.append(cb);
			componentHandler.upgradeElement(label[0]);
			cell.append(label);
			return cell;
		}
		
		function generateSeatOptions(){
			var rows = $([]);
			for(var i = 0; i < seats.length; ++i){
				var seat = seats[i];
				rows = rows.add(generateSeatOption(seat));
			}
			return rows;
		}
		
		function generateSeatOption(seat){
			var row = $('<tr>');
			var seatName = "Seat: Row " + seat.row + " Col " + String.fromCharCode(64 + seat.col);
			row.append($('<td class="mdl-data-table__cell--non-numeric">').text(seatName));
			row.append(generateSeatOptionCB(seat,"Adult"));
			row.append(generateSeatOptionCB(seat,"Child"));
			row.append(generateSeatOptionCB(seat,"Wheelchair"));
			row.append(generateSeatOptionCB(seat,"SpecialDiet"));
			return row;
		}
		
		function generateSeatOptionCB(seat,option){
			var row = seat.row;
			var col = seat.col;
			var checked = seat[option]?true:false;
			var cell = $('<td class="mdl-data-table__cell--non-numeric">');
			var label = $('<label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="checkboxOptions-'+row+'-'+col+'-'+option+'">');
			var cb = $('<input type="checkbox" id="checkboxOptions-'+row+'-'+col+'-'+option+'" class="mdl-checkbox__input" onclick="seatSelectedOptionChanged(this, '+row+', '+col+', \''+option+'\')"/>')
			if(checked){
				cb.prop('checked',true);
				label.addClass("is-checked");
			}
			label.append(cb);
			componentHandler.upgradeElement(label[0]);
			cell.append(label);
			return cell;
		}
		
		function next(){
			if(validateSeats())
			{
				var data = {bookingID:bookingID, route:route, seats:seats};
				callPHP("php/savebooking.php", data, saveSuccess);
			}
			//jQuery.redirect("",seats);
		}
		
		function validateSeats(){
			if(seats.length == 0){
				showMessage('Please select at lest on seat.');
				return false;
			}
			var success = true;			
			for(var i = 0; i < seats.length; ++i){
				if(!validateSeat(seats[i]))
					success = false;
			}
			return success;
		}
			
		function validateSeat(seat){
			if(seat['Adult'] && seat['Child']){
				showMessage("Error: You cannot select both Adult and Child for a Seat.");
				return false;
			}
			else if(!(seat['Adult'] || seat['Child'])){
				showMessage("Error: You must select either Adult or Child.");
				return false;	
			}
			return true;
		}
		
		function saveSuccess(status){
			if(status){
				window.location.assign("bookings.php")
			}
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
				<span class="mdl-layout-title">Seat Selection</span>
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
			<div class="page-content" style="text-align:center;">
				<h1>Seat Selection</h1>
				<table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp" style="margin:auto; width:auto">
				  <thead>
					 <tr>
						<th></th>
						<th class="mdl-data-table__cell--non-numeric">A</th>
						<th class="mdl-data-table__cell--non-numeric">B</th>
						<th class="mdl-data-table__cell--non-numeric">C</th>
						<th class="mdl-data-table__cell--non-numeric">D</th>
						<th class="mdl-data-table__cell--non-numeric">E</th>
					 </tr>
				  </thead>
				  <tbody id="SeatsTableBody">
				  </tbody>
				</table>
				<h1>Seat Options</h1>
				<table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp" style="margin:auto; width:auto">
					<thead>
						<tr>
							<th class="mdl-data-table__cell--non-numeric">Seat Name</th>
							<th class="mdl-data-table__cell--non-numeric">Adult</th>
							<th class="mdl-data-table__cell--non-numeric">Child</th>
							<th class="mdl-data-table__cell--non-numeric">Wheelchair</th>
							<th class="mdl-data-table__cell--non-numeric">Special Diet</th>
						</tr>
					</thead>
					<tbody id="OptionsTableBody">
					</tbody>
				</table>
				<br/>
				<br/>
				<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" onclick="next()" >Next</button>
				<br/>
				<br/>
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
