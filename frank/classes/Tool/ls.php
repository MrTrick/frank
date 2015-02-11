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

/* ls - the directory listing tool  */
class Tool_ls extends Tool {
	public static function description() { return 'Directory listing tool'; }

	/* Return help for this tool */
	public static function help($subtool=null) {
		return 
'ls - Directory listing tool
<b>Usage:</b> ls [DIRECTORY]
List information about the files in DIRECTORY (the current directory by default)
Sorts entries alphabetically. Does not show hidden files.

<b>Examples:</b>
&nbsp;&nbsp;&nbsp;<i>ls</i> 
List the contents of the current directory
&nbsp;&nbsp;&nbsp;<i>ls /</i>
List the contents of the root directory
&nbsp;&nbsp;&nbsp;<i>ls test</i>
List the contents of the test directory in the current directory
';
	}

	/*Run the command, with the given args.*/
	public static function run($args, &$session, $class=null) {	
		if (count($args) > 1) return Response::error("Too many arguments");
		$path=$session->path(isset($args[0]) ? $args[0] : null);
		$node = $session->computer->read($path, $session);
		if ($node===false) 
			return Response::error(); //"Folder does not exist");
		else if (!is_array($node))
			return Response::error('/'.implode('/',$path).' is a file - to view file contents, use cat');
	
		$list = array();
		foreach(array_keys($node) as $n) { 
			if ($n{0}=='.') continue;
			else if (is_object($node[$n])) $c='#ff0'; //Service
			else if (is_array($node[$n])) { $n.='/'; $c='#00f'; } //Folder
			else if ($node[$n] == $n.':'.md5(SALT.$n)) $c='#3bb'; //Binary
			else $c='#ddf'; //Normal file
			$list[$n] = $c;
		}
		uksort($list, create_function('$a,$b', 'return ($v=(substr($b,-1)=="/")-(substr($a,-1)=="/"))?$v:strcmp($a,$b);'));
		$r = "Contents of /".implode('/',$path).":\n";
		foreach($list as $name=>$color)
			$r.="&nbsp;<span style='color:$color'>$name</span>\n";
		if (!$list) $r= "<i>No files or folders</i>\n";
		return new Response($r);
	}
}
