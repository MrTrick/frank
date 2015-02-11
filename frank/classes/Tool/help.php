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
	public static function run($args, &$session, $class=null) {
		//Is there a command listed?
		$cmd = array_shift($args);
		if ($cmd) {
			if ($c = $session->computer->getTool($cmd, $session)) 
				return new Response( call_user_func_array(array($c,'help'), $args) );
			else return Response::error("Tool '$cmd' not found.");
		}
			
		//No command, instead list all the tools in the /bin folder
		$tools = array_keys($session->computer->read('/bin',  $session));
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
