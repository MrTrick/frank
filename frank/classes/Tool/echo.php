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

class Tool_echo extends Tool {
	public static function description() { return '...'; }
	public static function help($alias=null) { return '...'; }
	
	public static function run($args, &$session, $class=null) {
		return Response::error("not implemented yet");	
	}
}
