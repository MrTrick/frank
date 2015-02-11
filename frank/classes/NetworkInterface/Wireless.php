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


class NetworkInterface_Wireless extends NetworkInterface {
	public static function type() { return "Wireless 802.11a/b/g Interface"; }
	
	public static function start(&$computer, &$config, $key=false) {
		$network =& Network::$networks[$config['network']];
		if (!$key and $network->wep_key) 
			return setError("Access denied - key is required");
		else if ($key !== $network->wep_key) 
			return setError("Access denied - Incorrect key given");
		return parent::start($computer, $config);	
	}
}

?>
