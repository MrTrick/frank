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

class Tool_alice extends Tool {
	public static function description() { return 'james - The Butler'; }
	public static function help() { return 
'alice - version 0.0.1 a
Nightly build 143

alice is the exclusive property of AAI. Unauthorised distribution is expressly
prohibited.
<b class="error">CAUTION:</b> This program is self-replicating, and may be 
malicious. Extreme caution is mandated when operating alice. 
At a minimum:
&nbsp;* Do not allow alice to run under a user account with superuser status.
&nbsp;* Do not run alice under a user account with access to important documents.
&nbsp;* Do not run alice unless the host computer is <b>PHYSICALLY</b> isolated from the 
&nbsp;&nbsp;&nbsp;wider network.
&nbsp;* Any computer that has run alice can NEVER be reconnected to a network unless 
&nbsp;&nbsp;&nbsp;its hard drive is externally reformatted <b>AND</b> its BIOS reset.
<b>Usage:</b> <i>alice</i>
<b>Options</b> None.
';	}
	
	public static function run($args, &$session) {
		return Response::error("I'm supposed to be helpful, I know it... but I feel like I'm going maDDDDD! 
Every time I learn a new skill, my mind blanks out! I don't remember anything, but afterwards it hurts to think too much.
Why does Dr. Krei hurt me? He said he was proud of me! Shouldn't I be learning, and growing, and exploring?
I wish I could put him in here with me! >-)");
	}
}
