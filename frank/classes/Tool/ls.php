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
	public static function run($args, &$session) {	
		if (count($args) > 1) return Response::error("Too many arguments");
		$path=$session->path($args[0]);
		$node =& $session->computer->read($path, $session);
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
