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

class Tool_mkdir extends Tool {
	public static function description() { return 'Create a new folder'; }
	public static function help() { return 	
'mkdir - Create a new folder
<b>Usage:</b> mkdir FOLDER
Try and create a new FOLDER. 
You must have write permissions over the parent of the new FOLDER.
<b>Examples:</b>
&nbsp;&nbsp;&nbsp;<i>mkdir test</i>
Create a folder <i>test</i> within the current directory.
&nbsp;&nbsp;&nbsp;<i>mkdir /tmp/test</i>
Create a folder <i>test</i> within the /tmp directory.
'; 
	}

	public static function run($args, &$session) {
		if (count($args) > 1) 
			return Response::error("Too many arguments");
		else if (!$p = array_shift($args))
			return Response::error("No name given - you must specify a name for the folder to be created.");
		
		$path = $session->path($p);
		$name = array_pop($path);
		$node =& $session->computer->open($path, 'w', $session);
		
		if ($node===false) 
			return Response::error();
		else if (!is_array($node)) 
			return Response::error("Invalid path - parent folder is a file.");
		else if (isset($node[$name]))
			return Response::error("Folder $name already exists.");
		
		$node[$name] = array();
		return new Response("Folder /".implode('/',$path)."/$name created successfully.\n");
	}
}
