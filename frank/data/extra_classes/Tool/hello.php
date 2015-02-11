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
