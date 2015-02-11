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

class NetworkInterface {
	public static function type() { return "Ethernet Network Connection"; }
	
	public static function start(&$computer, &$config) { 
		$network =& Network::$networks[$config['network']];
		if ($config['status']=='down') {
			$network->connect($computer);
			$config['status'] = 'up'; 
			return true;
		}
		else return setError("Interface {$config['alias']} already active");
	}
	
	public static function stop(&$computer, &$config) { 		
		$network =& Network::$networks[$config['network']];
		if ($config['status']=='up') {
			$network->disconnect($computer);
			$config['status'] = 'down'; 
			return true;
		}
		else return setError("Interface {$config['alias']} already stopped");
	}
}

?>
