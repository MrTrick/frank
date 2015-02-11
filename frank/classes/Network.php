<?
/*------------------------------------------------------------------------------------------------
FRANK Engine and Game:
Copyright (c) 2008 - 2015 MrTrick
Description:
   Simulation of any number of connected computers, and their connections.
   Game content, plot, and tools for 'FRANK' game.
Creator:
   Patrick Barnes aka MrTrick  (mrtrick (at) mindbleach.com)
Web Location:
   http://mindbleach.com/frank
License:
   MIT
-------------------------------------------------------------------------------------------------*/

class Network {
	public static $networks = array();

	public $name; //Must be unique among networks...
	public $computers = array(); //The computers actively connected to this network
	public $wep_key; //Credentials needed for connecting to this network via wireless
	
	public function __construct($name, $wep_key=false) {
		$this->name = $name;
		$this->wep_key = $wep_key;
		if (isset(self::$networks[$name])) throw new Exception("The network $name already exists - cannot be created twice.");
		self::$networks[$name] =& $this;
	}
	public function connect(Computer &$computer) {
		$this->computers[$computer->name] =& $computer;
	}
	public function disconnect(Computer &$computer) {
		unset($this->computers[$computer->name]);
	}
}


?>
