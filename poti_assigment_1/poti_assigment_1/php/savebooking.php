<?php
include('common.php');

$bookings = getBookings();

$data = json_decode(file_get_contents('php://input'));

checkCreateBooking();

$currentBooking = $bookings->findBookingViaID($data->bookingID);

if($currentBooking === NULL){
	die(json_encode(false));
}

$currentBooking->setSeats(getSeats());

die(json_encode(true));


function checkCreateBooking(){
	global $data, $bookings;
	if($data->bookingID == -1){
		if($data->route !== NULL)
			$data->bookingID = $bookings->addBooking($data->route)->ID;
	}
}

function getSeats(){
	global $data;
	$seats = array();
	foreach($data->seats as $obj ) {
		$seat = new Seat();
		$seat->set($obj);
		array_push($seats, $seat);
	}
	return $seats;
}


?>