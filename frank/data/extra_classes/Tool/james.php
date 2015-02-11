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

class Tool_james extends Tool {
	public static function description() { return 'james - The Butler'; }
	public static function help() { return 
'james - The Butler
Version 1.1.25
Property of Applied Artificial Intelligence

<b>Usage:</b> james
This file will install the james butler AI on your computer.
For the install to be successful, you must have administrator rights while installing.

';	}
	
	public static function run($args, &$session) {
		return new Response("<b>Installing james - The Butler - on your system</b>\n".
		"Unpacking install files...\n".
		"Looking up dependencies...\n".
		"Copying files....<span class='error'>Could not copy files - insufficient permissions to install!</span>\n".
		":Reversing install process...\n".
		":Sorry, installation could not be completed. Make sure you are logged in as administrator before running this program.\n\n"
		);
	}
}
