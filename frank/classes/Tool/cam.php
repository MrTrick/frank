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

class Tool_cam extends Tool_Client {
	public static function description() { return 'Streaming video client'; }
	public static function service() { return 'camd'; }
	
	public static function run($args, &$session) { return parent::run($args, $session, __CLASS__); }
	public static function help() { return parent::help('cam'); }
}

?>
