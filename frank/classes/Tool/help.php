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


class Tool_Help extends Tool {
	public static function description() { return 'Help function'; }
	/* Return help for this tool */
	public static function help($subtool=null) {
		return 
'help - Help function
<b>Usage:</b> help [TOOL|SERVICE [EXTRA]*]
Show help for using a particular TOOL or SERVICE.
If extra information is available for that TOOL or SERVICE, the EXTRA argument 
can be used to select it.
';
	}
	
	/*Run the command, with the given args.*/
	public static function run($args, &$session) {
		//Is there a command listed?
		$cmd = array_shift($args);
		if ($cmd) {
			if ($c = $session->computer->getTool($cmd, $session)) 
				return new Response( call_user_func_array(array($c,'help'), $args) );
			else return Response::error("Tool '$cmd' not found.");
		}
			
		//No command, instead list all the tools in the /bin folder
		$tools =& array_keys($session->computer->read('/bin',  $session));
		sort($tools);
		$max = 0; foreach($tools as $t) if (strlen($t) > $max) $max = strlen($t);
		
		$r = "System Help:\nAvailable tools on ".$session->computer->name.":\n";
		foreach($tools as $tool) {
			$c = $session->computer->getTool($tool, $session);
			if ($c) $r.= sprintf(" % -{$max}s - %s\n", $tool, call_user_func(array($c,'description')));
		}
		$r.= "For more information on each tool, type 'help TOOLNAME'.\n";
		return new Response($r, R_PLAIN);
	}
}
?>