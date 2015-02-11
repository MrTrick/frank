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

class Tool_rm extends Tool {
	public static function description() { return 'Remove files or folders'; }
	public static function help($alias=null) { 
		return
'rm - Remove files or folders
<b>Usage:</b> rm TARGET
Remove the TARGET file or folder from the filesystem.
If the current user does not have write permissions over that file or folder, the operation will fail.
<b>Examples:</b>
&nbsp;&nbsp;&nbsp;<i>rm test.txt</i>
Remove the <i>test.txt</i> file in the current directory.
&nbsp;&nbsp;&nbsp;<i>rm /tmp/testdir/</i>
Remove the <i>testdir</i> folder from /tmp
&nbsp;&nbsp;&nbsp;<i>rm /home/joe</i>
Try to remove the <i>/home/joe</i> folder <i>(fails, user doesn\'t have permission)</i>
';
}
	public static function run($args, &$session, $class=null) {
		if (count($args) > 1) 
			return Response::error("Too many arguments");
		else if (!$p = array_shift($args))
			return Response::error("No target specified");
		//Identify the folder the file/folder to be deleted is in ($path), and the file/folder name ($target)
		$path = $session->path($p);
		$target = array_pop($path);
		if (!$target) return Response::error("Can't delete the root node!");
		else if ($target=='.permissions') return Response::error("Can't delete .permissions files!");
		//Try and grab the node the target is in...
		$node = $session->computer->open($path, "w", $session);
		if ($node===false) return Response::error();
		
		if (!isset($node[$target])) return Response::error("Target not found!");

		//Delete the target.
		unset($node[$target]);
		
		//Is PWD affected?
		array_push($path, $target);
		$pwd = array_slice($session->pwd, 0, count($path));
		if ($path == $pwd) {
			array_pop($path);
			$session->pwd = $path;
			return new Response("Target removed. Working directory changed to /".implode('/',$path)."\n");
		}
		
		return new Response("Target removed.\n");
	}
}
