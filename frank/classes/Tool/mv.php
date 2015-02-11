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

class Tool_mv extends Tool {
	public static function description() { return 'Move files or folders'; }
	public static function help($alias=null) { 
		return 
'mv - Move files or folders
<b>Usage:</b> mv SOURCE TARGET
Move the SOURCE file or folder to the TARGET location.
If a file or folder already exists at the TARGET location, the operation will fail. 
If the TARGET argument is an existing folder and ends with a /, SOURCE will be moved inside that folder.
<b>Examples:</b>
&nbsp;&nbsp;&nbsp;<i>mv test.txt new.txt</i>
Move the <i>test.txt</i> file in the current directory to file <i>new.txt</i>. <i>(Renames it)</i>
&nbsp;&nbsp;&nbsp;<i>mv /home/joe/test.txt .</i>
Move the file <i>/home/joe/test.txt</i> to the current directory.
&nbsp;&nbsp;&nbsp;<i>mv fol1 /tmp/fol1</i>
&nbsp;&nbsp;&nbsp;<i>mv fol1 /tmp/</i>
In both cases move the <i>fol1</i> directory to <i>/tmp/fol1</i>.
&nbsp;&nbsp;&nbsp;<i>mv fol1 /tmp</i>
Try to move the <i>fol1</i> directory to <i>/tmp</i> <i>(fails, because /tmp already exists)</i>

';
	}
	
	public static function run($args, &$session, $class=null) {
		if (count($args) > 2) return Response::error("Too many arguments");
		else if (!$s = array_shift($args))
			return Response::error("You must specify a source file or folder to move.");
		else if (!$t = array_shift($args))
			return Response::error("You must specify a target to move to.");
			
		$s_path = $session->path($s);
		$s_name = $s_path[count($s_path)-1];
		
		$t_path = $session->path($t);
		if (($c=substr($t,-1))=='/' or $c=='.') array_push($t_path, $s_name);
		
		//If the destination is a subfolder of the source, fail...
		$stub = array_slice($t_path, 0, count($s_path));
		if ($s_path == $stub)
			return Response::error("Invalid command - cannot move a folder to a subfolder of itself.");
		//Disable modifying any .permissions file...
		if ($s_name == '.permissions') return Response::error("Cannot modify .permissions files");			
				
		//Try and copy the file first...
		array_pop($s_path);
		$content =& $session->computer->open($s_path, 'w', $session);
		if ($content===false) 
			return Response::error("Source - ".getError());
		else if (!isset($content[$s_name]))
			return Response::error("Source - File not found");
		if (!$session->computer->write($t_path, $content[$s_name], $session))
			return Response::error("Target - ".getError());

		
		//If successful, remove the file from its old location
		unset($content[$s_name]);
		
		//Is PWD affected?
		$pwd = array_slice($session->pwd, 0, count($s_path));
		if ($pwd == $s_path) {
			$session->pwd = $pwd;
			return new Response("$s_name moved. Working directory changed to /".implode('/',$pwd)."\n");
		}		
		return new Response("$s_name moved.\n");
	}
}
