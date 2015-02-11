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

/* An abstract class that other tools descend from. Defines an interface all tools must follow.  
Tools are completely static - any state is to be stored in the relevant session's state_info

*/
class Tool {
	public static function description() { throw new Exception("Must override this function."); }
	public static function help($alias = null) { throw new Exception("Must override this function."); }
	public static function run($args, &$session, $class=null) { throw new Exception("Must override this function."); }
}
