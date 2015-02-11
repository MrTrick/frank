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