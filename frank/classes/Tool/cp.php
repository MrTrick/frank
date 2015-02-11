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

class Tool_cp extends Tool {
	public static function description() { return 'Copy files or folders'; }
	public static function help() { 
		return 
'cp - Copy files or folders
<b>Usage:</b> cp SOURCE TARGET
Copy the SOURCE file or folder to the TARGET location.
If a file or folder already exists at the TARGET location, the operation will fail. 
If the TARGET argument is an existing folder and ends with a /, SOURCE will be copied inside that folder.
<b>Examples:</b>
&nbsp;&nbsp;&nbsp;<i>cp test.txt new.txt</i>
Copy the <i>test.txt</i> file in the current directory to file <i>new.txt</i>.
&nbsp;&nbsp;&nbsp;<i>cp /home/joe/test.txt .</i>
Copy the file <i>/home/joe/test.txt</i> to the current directory.
&nbsp;&nbsp;&nbsp;<i>cp fol1 /tmp/fol1</i>
&nbsp;&nbsp;&nbsp;<i>cp fol1 /tmp/</i>
In both cases copy the <i>fol1</i> directory to <i>/tmp/fol1</i>.
&nbsp;&nbsp;&nbsp;<i>cp fol1 /tmp</i>
Try to copy the <i>fol1</i> directory to <i>/tmp</i> <i>(fails, because /tmp already exists)</i>

';
	}
	
	public static function run($args, &$session) {
		if (count($args) > 2) return Response::error("Too many arguments");
		else if (!$s = array_shift($args))
			return Response::error("You must specify a source file or folder to copy.");
		else if (!$t = array_shift($args))
			return Response::error("You must specify a target to copy to.");
			
		$s_path = $session->path($s);
		$s_name = $s_path[count($s_path)-1];
				
		$t_path = $session->path($t);
		if (($c=substr($t,-1))=='/' or $c=='.') array_push($t_path, $s_name);
		
		//Try and copy the file...
		$content = $session->computer->getFolderCopy($s_path, $session, $skipped);
		//$content = $session->computer->read($s_path, $session);
		if ($content===false) 
			return Response::error("Source - ".getError());
		//if ($_name=='.permissions') 
		//	return Response::error("Cannot copy permissions files");
		if (!$session->computer->write($t_path, $content, $session))
			return Response::error("Target - ".getError());
		if ($skipped) return new Response("$s_name partially copied - some files could not be read.\n");
		else return new Response("$s_name copied.\n");
	}
}
