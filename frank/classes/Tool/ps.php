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

class Tool_ps extends Tool {
	public static function description() { return 'Show installed services'; }
	public static function help($alias=null) { 
		return 
'ps - Show installed services
<b>Usage:</b> ps
Return a list of services installed on this machine.
<b>Options:</b> None.
';
	}
	
	public static function run($args, &$session, $class=null) {
		if ($args) return Response::error("Too many arguments");
		$services = array_keys($session->computer->getNode('/boot/'));
		$o="Installed services on {$session->computer->name}:\n";
		foreach ($services as $s) $o .= "&nbsp;*&nbsp;$s\n";
		return new Response($o);	
	}
}
