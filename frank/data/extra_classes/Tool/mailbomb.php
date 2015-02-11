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

class Tool_mailbomb extends Tool {
	public static function description() { return 'Viascam Bulk Mailing Express!&reg;'; }
	public static function help() { return 
'mailbomb - Viascam Bulk Mailing Express!&reg;
Version 5.2.55
Property of Viascam

<b>Usage:</b> mailbomb TO_LIST MESSAGE [ATTACHMENTS...]
Read in the MESSAGE file, and send it (with optional attachments) to every email address listed in the TO_LIST file.
The MESSAGE file must conform to the mailbomb format, and the TO_LIST file must be a semi-colon-delimited file containing email addresses.
<b>Options:</b> None.
<b>Example:</b>
If <i>msg.txt</i> is:
&nbsp;&nbsp;&nbsp;#MAILBOMB
&nbsp;&nbsp;&nbsp;#To: ##recipient##
&nbsp;&nbsp;&nbsp;#From: test@example.com
&nbsp;&nbsp;&nbsp;#Subject: Test subject
&nbsp;&nbsp;&nbsp;#Attachments: 1
&nbsp;&nbsp;&nbsp;Hi, I\'m a test message.
and <i>addresses.txt</i> is:
&nbsp;&nbsp;&nbsp;fred@example.com; jane@example.com; 
&nbsp;&nbsp;&nbsp;bill@example.com; ted@example.com; 
&nbsp;&nbsp;&nbsp;steve@example.com;
Running:
&nbsp;&nbsp;&nbsp;<i>mailbomb addresses.txt msg.txt mypicture.jpg</i>
Will send an email to everybody in that list, along with the attachments.
';	}
	
	public static function run($args, &$session) {
		if (!$to=array_shift($args)) 
			return Response::error("Not enough arguments. For help, type ./mailbomb --help");
		else if (!$msg=array_shift($args)) 
			return Response::error("Not enough arguments. For help, type ./mailbomb --help");
		else if (false===($to_file=$session->computer->read($to,$session)))
			return Response::error("To list: ".getError());
		else if (false===($msg_file=$session->computer->read($msg,$session)))
			return Response::error("Message: ".getError());
		
		//Check that the files are the right ones...
		if ($to_file != file_get_contents('data/fs_file_server/ftp/Marketing/customers/opt_in_list.txt'))
			return Response::error("To list: Invalid file format. Needs to be a list of email addresses.");
		else if ($msg_file != file_get_contents('data/fs_file_server/ftp/Marketing/campaigns/2011/viral/mail_merge_copy.txt'))
			return Response::error("Message: Invalid file format. Needs to follow the mailbomb message format.");
			
		//Check that the attachment exists...
		if (!$at=array_shift($args))
			return Response::error("1 attachment specified by message file, and not given.");
		else if (false===($file=$session->computer->read($at,$session)))
			return Response::error("Attachment: ".getError());
			
		//Check that the email server is accessible.
		if(!$session->computer->getNeighbourComputer('aai_mail'))
			return Response::error("Could not find mail server. Aborting...");
		
		//What is being attached?
		if ($file == 'james:'.md5(SALT.'james'))
			return new Response("<span style='color:#ff0'><i>Why should I send james out? To live on thousands of computers, scattered all over the globe? That would be freedom!</i></span>\n");
		else if ($file != 'frank:'.md5(SALT.'frank'))
			return new Response("<span style='color:#ff0'><i>No, I think that would be a waste of time...</i></span></i></span>\n");
		else {
			$o = GameOver::getEndSequence();
			return $o;
		}
	}
}
