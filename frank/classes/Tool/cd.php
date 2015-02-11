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

class Tool_cd extends Tool {
	public static function description() { return 'Change the current directory'; }
	/* Return help for this tool */
	public static function help($subtool=null) {
		return 
'cd - Change the current directory
<b>Usage:</b> cd [PATH]
Change the current directory to PATH, where PATH is a relative or absolute path.
If no argument is given, return the current directory.
<b>Examples:</b>
&nbsp;&nbsp;&nbsp;<i>cd</i> 
Change to the current user\'s home directory.

&nbsp;&nbsp;&nbsp;<i>cd /docs/info</i> 
Change to the /docs/info directory

&nbsp;&nbsp;&nbsp;<i>cd files</i> 
Change to the files directory within the current directory

&nbsp;&nbsp;&nbsp;<i>cd ..</i> 
Change to the directory above the current directory
';
	}
	
	/*Run the command, with the given args.*/
	public static function run($args, &$session) {
		if (count($args) > 1) 
			return Response::error("Too many arguments");
		else if (!count($args)) {
			$defaults = $session->getShellDefaults();
			$args = array($session->path($session->sub($defaults['pwd'])));
		}

		if (false===($path=$session->path($args[0])))
			return Response::error(); //Invalid path
		else if (false===($node=&$session->computer->read($path, $session)))
			return Response::error(); //Folder doesn't exist
		else if (!is_array($node))
			return Response::error("Not a folder");
		
		$session->pwd = $path;
		return new Response('/'.implode('/', $path).'<br/>');
	}
}
