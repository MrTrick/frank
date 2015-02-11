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

class Tool_ftp extends Tool_Client {
	public static function description() { return 'Remote file transfer client'; }
	public static function service() { return 'ftpd'; }
	
	public static function run($args, &$session, $class=null) { return parent::run($args, $session, __CLASS__); }
	public static function help($alias=null) { return parent::help('ftp'); }
}
