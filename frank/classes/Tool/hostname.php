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