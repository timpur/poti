<?php
$config = (object) array(
    'host' => 'localhost',
    'username' => 'user',
    'password' => 'user@01',
    'database' => 'test',
    'table' => (object) array(
        'name' => 'flights',
        'route' => 'route_no',
        'from' => 'from_city',
        'to' => 'to_city',
        'price' => 'price'
    )
);


class Flight{
    var $route;
    var $from;
    var $to;
    var $price;
    function __construct($route, $from, $to, $price ) {
        $this->route = $route;
        $this->from = $from;
        $this->to = $to;
        $this->price = $price;
    }
}


class Bookings{
	var $bookings;
	var $num;
	public function __construct() {
		$this->bookings = array();
		$this->num = 0;
	}

	public function addBooking($route){
		$booking = new Booking($this->num++,$route);
		array_push($this->bookings, $booking);
		return $booking;
	}

	public function clearBookings(){
		$this->bookings = array();
		$this->num = 0;
	}

	public function findBookingViaID($id){
		foreach($this->bookings as $booking){
			if($booking->ID == $id)
				return $booking;
		}
	}
}

class Booking{
	var $ID;
	var $route;
	var $seats;
	public function __construct($id, $route) {
		$this->ID = $id;
		$this->route = $route;
		$this->seats = array();
    }
}

?>
