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

//Given a prefix and a range, return a random computer name. (Be careful not to call more times than the range is specified over)
function get_computer_name($prefix, $from, $to) {
	static $taken=array();
	do {
		$name = $prefix.mt_rand($from,$to);
	} while($taken[$name]);
	$taken[$name]=true;
	return $name;
}
//Get a password from the short_words.txt file
function get_password() {
	static $words = null;
	if (!$words) $words = file('data/short_words.txt');
	$w = trim($words[mt_rand(0,count($words)-1)]);
	if (strlen($w) < 4) $w.=mt_rand(1000,9999);
	else $w.=mt_rand(0,99);
	return $w;
}
//Given a path, and an array of words to substitute over, recursively load each file and sub-directory into a data tree.
function load_tree($path, &$subs) {
	$name = substr(strrchr($path, "/"),1);
	if (in_array(strtolower($name), array('.','..','.svn'))) 
		return false; //Ignore subversion and meta-directories.
	
	//Is the node a file?
	if (is_file($path)) {
		$c = file_get_contents($path);
		if (substr($c,0,2)=='#!') return substr($c,2).':'.md5(SALT.substr($c,2)); //Binary file...replace with the proper internal form
		else return str_replace($subs[0],$subs[1], $c);
	}
	//Is the node a folder? 
	else if (is_dir($path)) {
		$out=array();
		if (!$handle = opendir($path)) 
			throw new Exception("Invalid path");
		while (false !== ($file = readdir($handle)))
			if (false !== ($o = load_tree($path.'/'.$file, $subs)))
				$out[$file] = $o;
		closedir($handle);
		return $out;
	}
	else throw new Exception("Unexpected node!");   
}
//Given an associative array, convert it into two substitution arrays (suitable for passing to str_replace)
//Adds the {$...} around the 'from' nodes.
//Returns a two-element array - {from, to}
function makesubs($vars) {
	$from=array();$to=array();
	foreach($vars as $k=>$v)
		if (is_string($v)) {$from[]="{\$$k}"; $to[]=$v;}
	return array($from,$to);
}