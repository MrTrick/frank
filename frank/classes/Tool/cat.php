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

               
class Tool_cat extends Tool {
	public static function description() { return 'File viewing tool'; }

	/* Return help for this tool */
	public static function help($subtool=null) {
		return 
'cat - File viewing tool
<b>Usage:</b> cat FILE
Read the contents of FILE, and display them on the screen.
<b>Example:</b> 
&nbsp;&nbsp;&nbsp;<i>cat readme.txt</i>
Display the contents of the readme.txt file in the current directory
';
	}

	/*Run the command, with the given args.*/
	public static function run($args, &$session, $class=null) {
		if (count($args) > 1) return Response::error("Too many arguments");
		$file = isset($args[0]) ? $args[0] : null;
		if (!$file) return Response::error("Missing argument, see 'help cat' for usage.");
		$node = $session->computer->read($file, $session);
		if ($node===false) return Response::error();
		else if (is_array($node)) return Response::error("$file is a folder - to view folder contents, use ls");
		else if (is_object($node)) return Response::error("$file is not readable");
		else return new Response($node.'<br/>');
	}
}
