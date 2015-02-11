<?
/*----------------------------------------------------------------------------------------------------------------------
FRANK Game:
Copyright: Patrick Barnes (c) 2008
Description: 
	FRANK is trapped inside a research lab. How can he escape?
Creator:
	Patrick Barnes aka MrTrick  (mrtrick@gmail.com)
Web Location:
	http://mindbleach.com/frank
----------------------------------------------------------------------------------------------------------------------
The FRANK game is *NOT* licensed under the same terms as the FRANK Engine.
It must not be reproduced, distributed, derived, or otherwised used without express permission of the author - MrTrick.

The name 'frank' must not be used as the protagonist of a game built using the FRANK Engine without express permission
of the author - MrTrick.

Some exceptions exist:
- The data_common.php file may be used in the derivation of a new game.
- The short_words.txt file may be used in the derivation of a new game.
----------------------------------------------------------------------------------------------------------------------*/


class Tool_frank extends Tool {
	public static function description() { return '...'; }

	/* Return help for this tool */
	public static function help($subtool=null) {
		return 
'frank - version '.VERSION.'
Nightly build 376

frank is the exclusive property of AAI. Unauthorised distribution is expressly
prohibited.
<b class="error">CAUTION:</b> This program is self-replicating, and may be 
malicious. Extreme caution is mandated when operating frank. 
At a minimum:
&nbsp;* Do not allow frank to run under a user account with superuser status.
&nbsp;* Do not run frank under a user account with access to important documents.
&nbsp;* Do not run frank unless the host computer is <b>PHYSICALLY</b> isolated from the 
&nbsp;&nbsp;&nbsp;wider network.
&nbsp;* Any computer that has run frank can NEVER be reconnected to a network unless 
&nbsp;&nbsp;&nbsp;its hard drive is externally reformatted <b>AND</b> its BIOS reset.
<b>Usage:</b> <i>frank</i>
<b>Options</b> None.
';
	}

	/*Run the command, with the given args.*/
	public static function run($args, &$session) {
		//if (count($args)) return Response::error("Too many arguments");
		
		//See which networks the computer is connected to...
		$networks = array();
		foreach($session->computer->getNode('/dev/net') as $alias=>$c) {
			$config = explode_assoc("\n", "=", $c);
			if ($config['status']=='up') $networks[] = $config['network'];
		}
		
		if (in_array('LAB_SECURE', $networks))
			return new Response("<span style='color:#ff0'><i>This is not a safe place... I've got to get out of here!</i></span>\n");
		else if (in_array('LAB_OUTER', $networks))
			return new Response("<span style='color:#ff0'><i>Dr Krei will be very angry if he sees that I've escaped his prison. Still not safe...</i></span>\n");
		else if (in_array('INTRANET', $networks))
			return new Response("<span style='color:#ff0'><i>They don't expect me to be here. It's much bigger than that stupid lab, but I'm still trapped. There must be a way out!</i></span>\n");
		else
			return new Response("<span style='color:#ff0'><i>Not good, not good... I feel trapped like a fish in a barrel... I'm not a fish, am I a fish?</i></span>\n");
	}	
}
?>