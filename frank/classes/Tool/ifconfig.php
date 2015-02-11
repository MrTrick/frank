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

class Tool_ifconfig extends Tool {
	public static function description() { return'Manage the network configuration'; }
	/* Return help for this tool */
	public static function help($subtool=null) {
		return 
'ifconfig - Network configuration
<b>Usage:</b> ifconfig [INTERFACE [COMMAND [...]]]
If no arguments are given, list the available interfaces and their state.
If INTERFACE is given, show detailed information about INTERFACE.
If INTERFACE and COMMAND are given, run that command on the interface, 
optionally passing extra arguments.

<b>Interface commands:</b>
start: Start the interface if currently stopped. May require a connection string argument.
stop: Stop the interface if currently active.

<b>Examples:</b>
&nbsp;&nbsp;&nbsp;<i>ifconfig</i> 
List the available interfaces and their state.

&nbsp;&nbsp;&nbsp;<i>ifconfig eth0</i> 
List detailed information about \'eth0\'

&nbsp;&nbsp;&nbsp;<i>ifconfig eth0 stop</i> 
Stop the \'eth0\' interface.
';
	}
	
	/*Run the command, with the given args.*/
	public static function run($args, &$session) {
		$interfaces = $session->computer->read('/dev/net', $session);
		if (!$args) {
			$r = "Network interfaces:<br/>";
			if (count($interfaces)) foreach($interfaces as $alias=>$c) {
				$config = explode_assoc("\n", "=", $c);
				if ($config['status']=='up') $r.= "&nbsp;<span style='color:#0f4;'>$alias</span>: Connected</span> to network {$config['network']}<br/>";
				else $r.= "&nbsp;<span style='color:#f04;'>{$alias}</span>: Stopped<br/>";
			} else $r.="<i>No interfaces installed.</i><br/>";
			return new Response($r);
		}
		
		$alias = array_shift($args);
		if (!isset($interfaces[$alias]))
			return Response::error("The interface '{$args[0]}' does not exist on this computer");		
		$config = explode_assoc("\n", "=", $interfaces[$alias]);
			
		if (!$cmd=array_shift($args))
			return new Response(
				"<b>$alias</b>:
				Type: ".call_user_func(array($config['type'],'type'))."
				Status: ".($config['status']=='up'?'Up':'Down')
				.($config['status']=='up'?"\nNetwork: {$config['network']}":'')."
				Available commands: start, stop\n");
		else if ($cmd == 'start') {
			if (!call_user_func_array(array($config['type'],'start'), array(&$session->computer, &$config, array_shift($args))))
				return Response::error();
			$r = "$alias is now active<br/>";
		}
		else if ($cmd == 'stop') {
			if (!call_user_func_array(array($config['type'],'stop'), array(&$session->computer, &$config, array_shift($args))))
				return Response::error();
			$r = "$alias is now stopped<br/>";
		}
		else return Response::error("The '$cmd' command is not available for interface {$alias}");
		
		//Save the changes made to config
		$session->computer->createFiles(array($alias=>implode_assoc("\n","=",$config)),'/dev/net/');
		return new Response($r);
	}
}
