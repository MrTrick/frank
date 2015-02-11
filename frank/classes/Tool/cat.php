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

/* ls - the directory listing tool  */
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
	public static function run($args, &$session) {
		if (count($args) > 1) return Response::error("Too many arguments");
		$file = $args[0];
		if (!$file) return Response::error("Missing argument, see 'help cat' for usage.");
		$node =& $session->computer->read($file, $session);
		if ($node===false) return Response::error();
		else if (is_array($node)) return Response::error("$file is a folder - to view folder contents, use ls");
		else if (is_object($node)) return Response::error("$file is not readable");
		else return new Response($node.'<br/>');
	}
}
?>