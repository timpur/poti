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
    var $rout;
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

?>
