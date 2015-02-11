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

class Tool_alice extends Tool {
	public static function description() { return 'james - The Butler'; }
	public static function help($alias=null) { return 
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
	
	public static function run($args, &$session, $class=null) {
		return Response::error("I'm supposed to be helpful, I know it... but I feel like I'm going maDDDDD! 
Every time I learn a new skill, my mind blanks out! I don't remember anything, but afterwards it hurts to think too much.
Why does Dr. Krei hurt me? He said he was proud of me! Shouldn't I be learning, and growing, and exploring?
I wish I could put him in here with me! >-)");
	}
}
