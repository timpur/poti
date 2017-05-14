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

function dbConnect(){
	global $config;
    $conn = new mysqli($config->host, $config->username, $config->password, $config->database);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
    return $conn;
}


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


function getBookings(){
	session_start();
	if(!isset($_SESSION["bookings"])){
		$_SESSION["bookings"] = new Bookings();
	}
	return $_SESSION["bookings"];
}

class Bookings{
	var $bookings;
	var $num;
	public function __construct() {
		$this->bookings = array();
		$this->num = 1;
	}

	public function addBooking($route){
		$booking = new Booking($this->num++,$route);
		array_push($this->bookings, $booking);
		return $booking;
	}

	public function clearBookings(){
		$this->bookings = array();
		$this->num = 1;
	}

	public function findBookingViaID($id){
		foreach($this->bookings as $booking){
			if($booking->ID == $id)
				return $booking;
		}
		return NULL;
	}
	
	public function findBookingIndexViaID($id){
		foreach($this->bookings as $key => $booking){
			if($booking->ID == $id)
				return $key;
		}
		return NULL;
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
	
	public function setSeats($seats){
		$this->seats = $seats;
	}
}

class Seat{
	var $row;
	var $col;
	var $Adult;
	var $Child;
	var $Wheelchair;
	var $SpecialDiet;
	
	public function __construct() {
		$this->row = -1;
		$this->col = -1;
		$this->Adult = false;
		$this->Child = false;
		$this->Wheelchair = false;
		$this->SpecialDiet = false;
    }
	
	public function set($obj) {
		foreach ($obj AS $key => $value) $this->{$key} = $value;
    }
}

?>
