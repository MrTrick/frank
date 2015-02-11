<?
/*----------------------------------------------------------------------------------------------------------------------
FRANK Engine:
Copyright: Patrick Barnes (c) 2008
Description: 
	Simulation of any number of connected computers, and their connections.
Creator:
	Patrick Barnes aka MrTrick  (mrtrick@gmail.com)
Web Location:
	http://mindbleach.com/frank
----------------------------------------------------------------------------------------------------------------------
The FRANK Engine is licensed under a creative commons license - Reproduction, distribution, and 
derivation are permitted, as long as the following conditions are upheld:
* The license is not changed - (Share-alike)
* Non-commercial use only - (No-commercial) 
* This header is left intact.
* Use of this software is attributed with a phrase such as 'using the FRANK engine' and a link to http://mindbleach.com/frank (Link-back)

This license does not cover the FRANK Game - see the frank/data folder for more information
----------------------------------------------------------------------------------------------------------------------*/

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
