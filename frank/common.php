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
define('VERSION', 'v1.2.0');
if (PHP_SAPI !== 'cli') session_start();
if (PHP_SAPI !== 'cli') header("Cache-control: private"); //IE 6 Fix for dodgy sessions handling...

//Secret salt - so no enterprising hacking type can use rainbow tables...
define('SALT', 'aliiiiiive!');

function __autoload($class_name) {
   //Fix the include path once
   static $fixed_path = false;
   if (!$fixed_path) { set_include_path( __DIR__.'/classes/' . PATH_SEPARATOR . __DIR__.'/data/extra_classes' . PATH_SEPARATOR . get_include_path()); $fixed_path = true; }

   $path = str_replace('_','/',$class_name).'.php';
   require_once( $path );
}
//--------------------------------------------------------------------------------------------------------------------
$error = '';
function &setError($e) {
	global $error;
	static $false=false;
	$error = $e;
	return $false;
}
function getError() {
	global $error;
	return $error;
}
//--------------------------------------------------------------------------------------------------------------------
define("R_PLAIN", 0);
define("R_HTML", 1);
define("R_TAGS", 2);
class	Response {
	public static function error($msg=null) { 
		return new Response("<span class='error'>".nl2br($msg?$msg:getError())."</span><br/>"); 
	}

	public function __construct($message, $mode=R_TAGS, $history='keep') {
		$this->stdout = $mode==R_TAGS ? nl2br($message) : $message;
		$this->html_mode = $mode==R_HTML || $mode==R_TAGS;
		if (!in_array($history, array('keep','push','pop'))) throw new Exception("Invalid history mode!");
		$this->history=$history;
	}

	public $stdout;
	public $html_mode;
	public $history;
	public $version=VERSION;
}
   
//--------------------------------------------------------------------------------------------------------------------
function getDateStamp($time=null) {
	return date('Y-m-d H:i:s',$time?$time:Loader::$frank['time']);
}
//--------------------------------------------------------------------------------------------------------------------
function explode_assoc($sep_prop, $sep_key, $string) {
	$out = array();
	foreach (explode($sep_prop, trim(trim($string), $sep_prop)) as $property) if ($property) { 
		list($k,$v) = explode($sep_key,$property);
		$out[$k]=$v;
	}
	return $out;
}
function implode_assoc($sep_prop, $sep_key, $array) {
	$out = '';
	foreach($array as $k=>$v) $out .= $k.$sep_key.$v.$sep_prop;
	return $out;
}
//--------------------------------------------------------------------------------------------------------------------

