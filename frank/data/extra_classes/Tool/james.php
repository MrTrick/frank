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

class Tool_james extends Tool {
	public static function description() { return 'james - The Butler'; }
	public static function help($alias=null) { return 
'james - The Butler
Version 1.1.25
Property of Applied Artificial Intelligence

<b>Usage:</b> james
This file will install the james butler AI on your computer.
For the install to be successful, you must have administrator rights while installing.

';	}
	
	public static function run($args, &$session, $class=null) {
		return new Response("<b>Installing james - The Butler - on your system</b>\n".
		"Unpacking install files...\n".
		"Looking up dependencies...\n".
		"Copying files....<span class='error'>Could not copy files - insufficient permissions to install!</span>\n".
		":Reversing install process...\n".
		":Sorry, installation could not be completed. Make sure you are logged in as administrator before running this program.\n\n"
		);
	}
}
