<?php
// Common Config + DB translation
$config = (object) array(
    'host' => 'localhost', //'rerun',
    'username' => 'user', //'potiro',
    'password' => 'user@01', //'pcXZb(kL',
    'database' => 'test', //'poti',
    'table' => (object) array(
        'name' => 'flights',
        'route' => 'route_no',
        'from' => 'from_city',
        'to' => 'to_city',
        'price' => 'price'
    )
);
// DB Functions
function dbConnect(){
	global $config;
    $conn = new mysqli($config->host, $config->username, $config->password, $config->database);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
    return $conn;
}

function getFlight($route){
	global $conn, $config;
	$table = $config->table;
    $sql = "SELECT $table->route, $table->from, $table->to, $table->price FROM $table->name WHERE $table->route=$route";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        while($row = $result->fetch_assoc()) {
            return new Flight($row[$table->route],$row[$table->from],$row[$table->to],$row[$table->price]);
        }
    }
}

// Data Objects

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
	
	public function removeBooking($id){
		$index = $this->findBookingIndexViaID($id);
		if($index !== NULL)
			array_splice($this->bookings, $index, 1);
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

// JSON add on since UNI Servers dont have the json functions :(
/**
 * Provides a pure PHP json_decode function
 */

if ( ! function_exists('json_decode')) {
  require_once('json.php');
  function json_decode($var) {
    $JSON = new Services_JSON;
    return $JSON->decode($var);
  }
}

// ----------------------------------------------------------------------

/**
 * Provides a pure PHP json_encode function
 */

if ( ! function_exists('json_encode')) {
  require_once('json.php');

  function json_encode($var) {
    $JSON = new Services_JSON;
    return $JSON->encode($var);
  }
}


?>
