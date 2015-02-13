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

class Tool_ping extends Tool {
	public static function description() { return 'Network diagnosis tool'; }

	/* Return help for this tool */
	public static function help($subtool=null) {
		return 
'ping - Network diagnosis tool
<b>Usage:</b> ping COMPUTER|(-broadcast INTERFACE)
Send a message to COMPUTER, or send a broadcast message to all computers on INTERFACE\'s network, to see whether a response comes back.

<b>Examples:</b>
&nbsp;&nbsp;&nbsp;<i>ping webserv</i> 
Check whether the webserv computer is accessible.

&nbsp;&nbsp;&nbsp;<i>ping -broadcast eth0</i>
Check which computers on eth0\'s network are accessible.
';
	}

	/*Run the command, with the given args.*/
	public static function run($args, &$session, $class=null) {
		if (count($args) > 2) return Response::error("Too many arguments");
		//Load the ethernet interfaces on that computer
		$interfaces = $session->computer->read('/dev/net', $session);
		//No hostname specified
		if (!$hostname = array_shift($args))
			return Response::error('You must specify a computer name or the -broadcast option and an interface name.');
		//Single target ping name
		else if ($hostname != '-broadcast') {
			if (!$session->computer->getNeighbourComputer($hostname))
				return Response::error();
			else return new Response("$hostname responds.<br/>");
		}
		//Broadcast mode
		else if (!$if_name=array_shift($args)) 
			return Response::error('If using the -broadcast option you must specify an interface to broadcast over.');
		else if (!isset($interfaces[$if_name]) || !$config=explode_assoc("\n","=",$interfaces[$if_name]))
			return Response::error("Interface $if_name does not exist");
		else if ($config['status']!='up') 
			return Response::error("$if_name is inactive");
		else {
			$r = "Accessible computers on network {$config['network']}:\n";
			$c_list = Network::$networks[$config['network']]->computers;
			ksort($c_list);
			foreach ($c_list as $name=>$computer)
				$r.=" $name responds.\n";
			return new Response($r);
		}
	}
}
