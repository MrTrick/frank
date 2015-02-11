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

class Tool_hello extends Tool {
	public static function description() { return 'Executable file'; }
	public static function help() { return 
'hello.c - compiled 2011/01/24 14:16:01
'; 
	}
	
	public static function run($args, &$session) {
		return new Response("Hello world!\n");
	}
}
