<?php
include('php/common.php');

$data = json_decode(file_get_contents('php://input'), true);

session_start();

$bookings = getBookings();
$bookings->clearBookings();
$currentBooking = getCurrentBooking($bookings);

if(!isset($currentBooking)){
	die("Error: No vailid booking ID was given");
}
	

//echo json_encode($currentBooking)."<br/>";
//echo json_encode($bookings);




function getCurrentBooking($bookings){
	$booking = checkNewSessionBooking($bookings);
	if(!isset($booking)){
		$bookingID = isset($_REQUEST["bookingid"]) ? $_REQUEST["bookingid"] : null;
		if($bookingID){
			$booking = $bookings->findBookingViaID($bookingID);
		}
	}
	return $booking;
}

function checkNewSessionBooking($bookings){
	$selectedFlightNo = isset($_REQUEST["selection"]) ? $_REQUEST["selection"] : null;
	if($selectedFlightNo){
		return $bookings->addBooking($selectedFlightNo);
	}
	return null;
}

function getBookings(){
	if(!isset($_SESSION["bookings"])){
		$_SESSION["bookings"] = new Bookings();
	}
	return $_SESSION["bookings"];
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
	<style type="text/css"></style>
	<script type="text/javascript">
		var seats = [{row:1,col:1,Child:true}];
		
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
			row.append(generateSeatOptionCB(seat,"Special Diet"));
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
				
			}
			//jQuery.redirect("",seats);
		}
		
		function validateSeats(){
			if(seats.length == 0)
				return false;
			var success = true;			
			for(var i = 0; i < seats.length; ++i){
				if(!validateSeat(seats[i]))
					success = false;
			}
			return success;
		}
		
		function validateSeat(seat){
			if(seat['Adult'] || seat['Child'])
				return true;			
			return false
		}
		
	</script>
</head>
<body>
	<div style="text-align:center;">
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
	</div>
	<div style="text-align:center;">
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
	</div>
	<div style="text-align:center;">
		<br/>
		<br/>
		<button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored" onclick="next()" >Next</button>
	</div>
</body>
</html>
