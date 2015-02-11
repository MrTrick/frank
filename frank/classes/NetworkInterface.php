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
