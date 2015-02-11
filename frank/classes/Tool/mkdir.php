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