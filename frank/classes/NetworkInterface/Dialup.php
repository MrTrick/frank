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


class NetworkInterface_Dialup extends NetworkInterface {
	public static function type() { return "56K v.92 PCMCIA Modem"; }
	
	public static function start(&$computer, &$config, $number=false) {
		return setError($number ? "No dial tone detected" : "No phone number specified - usage: start PHONENUMBER");
	}
}

