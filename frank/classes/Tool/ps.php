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

class Tool_ps extends Tool {
	public static function description() { return 'Show installed services'; }
	public static function help() { 
		return 
'ps - Show installed services
<b>Usage:</b> ps
Return a list of services installed on this machine.
<b>Options:</b> None.
';
	}
	
	public static function run($args, &$session) {
		if ($args) return Response::error("Too many arguments");
		$services = array_keys($session->computer->getNode('/boot/'));
		$o="Installed services on {$session->computer->name}:\n";
		foreach ($services as $s) $o .= "&nbsp;*&nbsp;$s\n";
		return new Response($o);	
	}
}
