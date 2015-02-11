<?
/*----------------------------------------------------------------------------------------------------------------------
FRANK Engine:
Copyright: Patrick Barnes (c) 2008
Description: 
	Simulation of any number of connected computers, and their connections.
Creator:
	Patrick Barnes aka MrTrick  (mrtrick@gmail.com)
Web Location:
	http://mindbleach.com/frank
----------------------------------------------------------------------------------------------------------------------
The FRANK Engine is licensed under a creative commons license - Reproduction, distribution, and 
derivation are permitted, as long as the following conditions are upheld:
* The license is not changed - (Share-alike)
* Non-commercial use only - (No-commercial) 
* This header is left intact.
* Use of this software is attributed with a phrase such as 'using the FRANK engine' and a link to http://mindbleach.com/frank (Link-back)

This license does not cover the FRANK Game - see the frank/data folder for more information
----------------------------------------------------------------------------------------------------------------------*/

/* An abstract class that other tools descend from. Defines an interface all tools must follow.  
Tools are completely static - any state is to be stored in the relevant session's state_info

*/
abstract class Tool {
	abstract public static function description();
	abstract public static function help();	
	abstract public static function run($args, &$session);
}
