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

class Tool_hostname extends Tool {
	public static function description() { return 'Identify this computer'; }

	/* Return help for this tool */
	public static function help($subtool=null) {
		return 
'hostname - Identify this computer
<b>Usage:</b> hostname
Display the name of this computer.
<b>Options:</b> none
';
	}

	/*Run the command, with the given args.*/
	public static function run($args, &$session) {
		if ($args) return Response::error('Too many arguments');
		else return new Response($session->computer->name."<br/>");
	}
}
?>
