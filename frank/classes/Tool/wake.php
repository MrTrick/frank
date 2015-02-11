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

class Tool_wake extends Tool {
	public static function description() { return 'Wake-on-lan tool'; }
	public static function help() { return 
	'wake - Wake-on-lan tool
<b>Usage:</b> wake COMPUTER
Send out a WOL packet across the network. 
If a computer is attached to the network but switched off, AND is configured 
to \'wake up\' when it receives a magic WOL packet, it boot up and connect 
to the network.
<b>Examples:</b>
&nbsp;&nbsp;&nbsp;<i>wake PC_FRED</i>
Send a WOL packet addressed to PC_FRED. If PC_FRED is attached to one of the 
same networks as the sending computer, it will boot up, and respond.
if PC_FRED is already running, this command has no effect.
'; 
	}
	
	public static function run($args, &$session) {
		if (count($args) > 1) 
			return Response::error("Too many arguments");
		else if (!$target_name = array_shift($args))
			return Response::error("You must specify a computer to address the WOL packet to.");
			
		//Get a list of networks this computer is attached to...
		$local_networks = array();
		foreach($session->computer->getNode('/dev/net') as $alias=>$c) {
			$config = explode_assoc("\n", "=", $c);
			if ($config['status']=='up' and Network::$networks[$config['network']]) $local_networks[] = $config['network'];
		}
		//Find the target computer
		if (!$target =& Computer::$computers[$target_name])
			return Response::error("No response..."); //Doesn't exist, so can't be woken. ;-)
		
		//Look through the networks the target computer has...
		
		//If a decoy computer....
		if (!is_array($interfaces = $target->getNode('/dev/net'))) {
			//See if it's on a network already...
			if ($session->computer->getNeighbourComputer($target_name)) 	return new Response("$target_name responds, already active.\n");
			else return Response::error("No response...");
		}
		//Normal computer, check each interface to see if it's on the same one as the caller...
		else foreach($interfaces as $alias=>$c) {
			$config = explode_assoc("\n", "=", $c);
			//Found a common network?
			if (array_search($config['network'], $local_networks)!==false) {
				if($config['status']=='up') return new Response("$target_name responds, already active.\n");
				//Can only wake if the wake-on-lan file is in the configuration folder...
				//and the interface can start...
				if($target->getNode('/etc/wol')=='enabled'
				and call_user_func_array(array($config['type'], 'start'), array(&$target, &$config))) {
					//Successful, save the change in network configuration, and return an indicative response.
					$target->createFiles(array($alias=>implode_assoc("\n","=",$config)), '/dev/net/');
					return new Response("$target_name has woken, and is connected to {$config['network']}\n");
				}
				else
					return Response::error("No response...");
			}
		}
		//Checked every network, without luck:
		return Response::error("No response...");
	}
}
