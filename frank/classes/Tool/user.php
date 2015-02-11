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

class Tool_user extends Tool {
	public static function description() { return 'List the current user'; }

	/* Return help for this tool */
	public static function help($subtool=null) {
		return 
'user - Identify user
<b>Usage:</b> user
Return the currently logged-in user.
<b>Options:</b> None.
';
	}

	/*Run the command, with the given args.*/
	public static function run($args, &$session) {
		if (!count($args)) return new Response($session->user."\n");
		else return Response::error("Too many arguments");
	}
}
?>
