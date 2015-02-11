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

class Tool_su extends Tool {
	public static function description() { return 'Change user'; }
	public static function help() { return 
	'su - Change current user
<b>Usage:</b> su [USER]
Try and open a new session as USER. If not specified, USER is <i>root</i>.
When within that session, \'exit\' will close it and return to the previous session.
<b>Examples:</b>
&nbsp;&nbsp;&nbsp;<i>su joe</i>
Try to switch user to <i>joe</i>. If <i>joe</i>exists, a password prompt will appear.
If the correct password is given, a new session will open with <i>joe</i> as user.
'; 
	}
	
	public static function run($args, &$session) {
		if (count($args) > 1) return Response::error("Too many arguments");
		if (!$user=array_shift($args)) $user = 'root';
		$session->attach(array(__CLASS__, 'login'));
		$session->state_info['user'] = $user;
		$out = new Response($user=='root'?'root password: ':'password: ');
		$out->history='push';
		return $out;
	}
	
	public static function login($input, &$session) {
		//If no password given or authentication failure, drop out
		if (!$input || !$session->computer->authenticate($session->state_info['user'], $input)) {
			$session->detach(); 
			$out = Response::error("Invalid credentials");
			$out->history='pop';
			return $out;
		}
		//Otherwise, success!... start a new session.
		else {
			$session->next = new Session($session->computer, $session->state_info['user']);
			if ($session->state_info['user']=='root') $session->next->prompt{strlen($session->next->prompt)-1}='#';
			$session->attach(array(__CLASS__, 'execute'));
			return new Response($session->next->sub("Logged in as \$user\n".$session->next->prompt));
		}
	}
	
	public static function execute($input, &$session) {
		//If the session handles the request, return it.
		if ($r = $session->next->execute($input)) return $r;
		//If the user wants to exit, drop back...
		else if (trim($input) == 'exit') {
			$user = $session->next->user;
			$session->next = null;
			$session->detach();
			$out = new Response("$user logged out.\n");
			$out->history='pop';
			return $out;
		}
		else throw new Exception("Session->execute returned false under unknown circumstances");
	}
}
