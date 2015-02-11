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

class Tool_find extends Tool {
	public static function description() { return 'Search tool'; }
	public static function help($alias=null) { return 
'find - Search tool
<b>Usage:</b> find [QUERY] [FOLDER]
Search for files or folders matching the QUERY options. 
If no QUERY options are specified, return all files and folders within FOLDER.
If FOLDER is not specified, the current directory is assumed.
<b>Options:</b>
&nbsp;&nbsp;-n=PATTERN
Return files and folders whose name matches the pattern.
&nbsp;&nbsp;-c=PATTERN
Returns files that contain a match for the pattern (no folders will be returned).

PATTERN may contain literal characters (but not spaces), and wildcards * and ?.
If multiple Options are specified, only files and folders that match ALL conditions are returned.
<b>Examples:</b>
&nbsp;&nbsp;&nbsp;<i>find -n=readme*</i>
Search within the current directory for files and folders whose name starts with <i>readme</i>.
&nbsp;&nbsp;&nbsp;<i>find -c=jump</i>
Search within the current directory for files that contain the word <i>jump</i>.

'; 
	}
	
	private static function find($contents, $name, &$args) {
		if ($name=='.permissions') return; //Don't return any permissions files
		
		//Is this node a directory?
		if (is_array($contents)) {
			if($name) array_push($args['path'], $name);
			array_walk($contents, array(__CLASS__, 'find'), $args);
			array_pop($args['path']);
			if (isset($args['content_filters'])) return; //Drop out early if there are any content filters active...
		}
		foreach($args['name_filters'] as $filter)
			if (!preg_match($filter, $name)) return; //Ignore any files or folders that don't match a name filter
		foreach($args['content_filters'] as $filter)
			if (!is_string($contents) || !preg_match($filter, $contents)) return; //Ignore any files that don't match a name filter, or are objects
		//Matches!
		$x = $args['path']; $x[] = $name;
		$args['out'][] = '/'.implode('/',$x);
	}
	
	public static function run($args, &$session, $class=null) {
		$path = null;
		$name_filters = array();
		$content_filters = array();
		//Process all the arguments
		foreach($args as $term) {
			//Folder or query term?
			if ($term{0} == '-') {
				if (!preg_match('/^-(.)=(.+)$/', $term, $m) || ($m[1]!='n' and $m[1]!='c'))
					return Response::error("<i>$term</i> is not a valid option");
				//Remove any runs of *'s, then escape any other regex chars, then convert the (now escaped) * and ? chars into valid regex.
				$pattern = preg_replace("/\\\\\\*+/","*",$m[2]);
				$pattern = preg_quote($pattern, '/');
				$pattern = preg_replace(array("/\\\\\\*/","/\\\\\\?/"), array('.*','.'), $pattern);
									
				if ($m[1]=='n') $name_filters[] = '/^'.$pattern.'$/';
				else if ($m[1]=='c') $content_filters[] = '/'.$pattern.'/';
			}
			else if ($path) 
				return Response::error("Folder is already specified! <i>$term</i> is an invalid argument.");
			else if (!is_array($path=$session->path($term)))
				return Response::error();
		}
		if (!$path) $path = $session->pwd;

		$start = $session->computer->getFolderCopy($path, $session);
		if (!$start) 
			return Response::error();
		else if (!is_array($start)) 
			return Response::error("Not a folder.");
		
		$name = array_pop($path);
		$out = array();
		$args = array('name_filters'=>&$name_filters, 'content_filters'=>&$content_filters, 'out'=>&$out, 'path'=>&$path);
		//var_dump($args); die;
		self::find($start,$name,$args);
		return new Response(implode("\n",$out).($out?"\n":'')."Results: ".count($out)."\n");
		
	}
}
