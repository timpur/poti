<?php
include('common.php');

$bookings = getBookings();

$data = json_decode(file_get_contents('php://input'), true);

$currentBooking = $bookings->findBookingViaID($data["bookingID"]);

if(!isset($currentBooking)){
	die(json_encode(false));
}

$currentBooking->setSeats(getSeats());

die(json_encode(true));

function getSeats(){
	global $data;
	$seats = array();
	foreach($data["seats"] as $obj ) {
		$seat = new Seat();
		$seat->set($obj);
		array_push($seats, $seat);
	}
	return $seats;
}

?>