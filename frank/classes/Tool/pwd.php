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

class Tool_pwd extends Tool {
	public static function description() { return 'List the current directory'; }

	/* Return help for this tool */
	public static function help($subtool=null) {
		return 
'pwd - Current Directory tool
<b>Usage:</b> pwd
Show the current directory.
<b>Options:</b> None.
';
	}

	/*Run the command, with the given args.*/
	public static function run($args, &$session) {
		if (!count($args)) return new Response('/'.implode('/', $session->pwd).'<br/>');
		else return Response::error("Too many arguments");
	}
}
?>
