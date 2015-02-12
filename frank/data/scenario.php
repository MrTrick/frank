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

require_once('data/data_common.php');
//----------------------------------------------------------------------------------------
//[ FRANK   ] --. 
//[ ALICE    ] --\
//[ file    ] --+--[LAB_SECURE]
//[ cam     ] --/
//              \----eth0[ laptop ]eth1.
//                                     |
//                  ,------------------'
//[ workstation ] --\
//[ printer     ] --'---[LAB_OUTER]
//[ server      ] --\
//                   `------eth1[ gateway ]eth0--.
//                                               |
//                    ,--------------------------'
//                    |
//[ file server ]--[ INTRANET ]-- [ lots of useless computers and printers ]
//                    |
//[ mail_server ] ---' `--- [ web_prox ] 
//----------------------------------------------------------------------------------------
$TIME = mktime(0,0,0,03,19,2011)+mt_rand(1,60*60);
//----------------------------------------------------------------------------------------
$LAB_WEP_KEY = substr(md5(mt_rand()),-12);

$FRANK_PASSWORD = 'shelley';
$ALICE_PASSWORD = 'hackthegibson';
$BOB_PASSWORD = get_password();
$SEYMOUR_PASSWORD = get_password();
//----------------------------------------------------------------------------------------
$_taken_computer_names = array();
$HOME_NAME = get_computer_name('lab_',1,10);
$ALICE_NAME = get_computer_name('lab_',1,10);
$SECSERV_NAME = get_computer_name('lab_',1,10);

$LAPTOP_NAME = get_computer_name('corp_',4000,7000);

$OUTSERV_NAME = 'lab_server';
$OUTPRINT_NAME = 'lab_printer';
$OUTWORK_NAME = 'lab_workstation_1';

$CORP_BOB_NAME = get_computer_name('corp_',4000,7000);
$CORP_FILE_NAME = 'aai_files';
$CORP_PROX_NAME = 'aai_web';
$CORP_MAIL_NAME = 'aai_mail';
//----------------------------------------------------------------------------------------
$subs = makesubs(get_defined_vars());
//----------------------------------------------------------------------------------------
//Remove any existing networks or computers
Network::clear();
Computer::clear();

//----------------------------------------------------------------------------------------
//Computer Networks
//----------------------------------------------------------------------------------------
$lab_secure = new Network('LAB_SECURE');
$lab_outer = new Network('LAB_OUTER',$LAB_WEP_KEY);
$intranet = new Network('INTRANET');
//----------------------------------------------------------------------------------------
//LAB_SECURE computers
//----------------------------------------------------------------------------------------

$home = new Computer($HOME_NAME);
	$home->addNetworkInterface('eth0', $lab_secure);
	$home->addUser('frank', $FRANK_PASSWORD);
	$home->addUser('bob', $BOB_PASSWORD);
	$home->addUser('seymour', $SEYMOUR_PASSWORD);
	$home->addTool('frank', '/home/frank');
	$home->addService('sshd');
	$home->addService('ftpd');
	$home->createFiles(load_tree('data/fs_home', $subs));

//----------------------------------------------------------------------------------------

$alice = new Computer($ALICE_NAME);
	$alice->addNetworkInterface('eth0', $lab_secure);
	$alice->addUser('alice', $ALICE_PASSWORD);
	$alice->addUser('seymour', $SEYMOUR_PASSWORD);
	$alice->addService('sshd');

//----------------------------------------------------------------------------------------

//Load the camera's view, and text-substitute any variables on the 'screen'
list($image, $dimensions) = Service_camd::loadImage("data/ai_lab_cam.txt");
$f=array();$t=array();foreach(get_defined_vars() as $k=>$v) if(is_string($v)) { $f[]="{\$$k}"; $t[]=str_pad($v, strlen($k)+3); }
foreach($image as &$row) $row = str_replace($f, $t, $row);
$CAMERA = array('image'=>$image, 'dimensions'=>$dimensions, 'camera'=>array(68,0));

$ss = new Computer($SECSERV_NAME);
	$ss->addNetworkInterface('eth0', $lab_secure);
	$ss->addUser('bob', $BOB_PASSWORD);
	$ss->addUser('seymour', $SEYMOUR_PASSWORD);
	$ss->addTool('cam');
	$ss->addService('camd', $CAMERA);
	$ss->addService('sshd');
	$ss->addService('ftpd');
	$ss->createFiles(load_tree('data/fs_ss', $subs));
	
//----------------------------------------------------------------------------------------
	
$laptop = new Computer($LAPTOP_NAME);
	$laptop->addNetworkInterface('eth0', $lab_secure, 'NetworkInterface', false);
	$laptop->createFiles(array('wol'=>'enabled'), '/etc/');
	$laptop->addNetworkInterface('wg0', $lab_outer, 'NetworkInterface_Wireless');
	$laptop->addNetworkInterface('dialup0', null, 'NetworkInterface_Dialup');
	$laptop->addUser('bob', $BOB_PASSWORD);
	$laptop->addService('sshd');
	$laptop->addService('ftpd');
	$laptop->addTool('cam');
	$laptop->createFiles(load_tree('data/fs_laptop', $subs));

//----------------------------------------------------------------------------------------
//LAB_OUTER computers
//----------------------------------------------------------------------------------------

$printer = new Computer_Printer($OUTPRINT_NAME, $lab_outer);

//----------------------------------------------------------------------------------------

$os = new Computer($OUTSERV_NAME);
	$os->addNetworkInterface('eth0', $lab_outer);
	$os->addNetworkInterface('pbx', null, 'NetworkInterface_Dialup');
	$os->addUser('bob', $BOB_PASSWORD);
	$os->addUser('seymour', $SEYMOUR_PASSWORD);
	$os->addService('sshd');
	$os->addService('ftpd');
	$os->createFiles(load_tree('data/fs_os', $subs));

//----------------------------------------------------------------------------------------

$work = new Computer($OUTWORK_NAME);
	$work->addNetworkInterface('eth0', $lab_outer);
	$work->addNetworkInterface('eth1', $intranet, 'NetworkInterface', false);
	$work->addUser('seymour', $SEYMOUR_PASSWORD);
	$work->addService('sshd');
	$work->addService('ftpd');
	$work->createFiles(load_tree('data/fs_work', $subs));

//----------------------------------------------------------------------------------------
//INTRANET computers
//----------------------------------------------------------------------------------------

$corp_bob = new Computer($CORP_BOB_NAME);
	$corp_bob->addNetworkInterface('eth0', $intranet);
	$corp_bob->addUser('bob', $BOB_PASSWORD);
	$corp_bob->addService('sshd');
	$corp_bob->createFiles(load_tree('data/fs_corp_bob', $subs));
	
//----------------------------------------------------------------------------------------

$file_server = new Computer($CORP_FILE_NAME);
	$file_server->addNetworkInterface('eth0', $intranet);
	$file_server->addService('ftpd');
	$file_server->createFiles(load_tree('data/fs_file_server', $subs));
	
//----------------------------------------------------------------------------------------

$prox = new Computer($CORP_PROX_NAME);
	$prox->addNetworkInterface('eth0', $intranet);
	$prox->addService('sshd'); //but no users, so nobody can log in but root.

//----------------------------------------------------------------------------------------

$mail = new Computer($CORP_MAIL_NAME);
	$mail->addNetworkInterface('eth0', $intranet);
	$mail->addService('sshd'); //but no users, so nobody can log in but root.

//----------------------------------------------------------------------------------------

for($i=0;$i<100;$i++)
	new Computer_Decoy(get_computer_name('corp_',4000,7000), $intranet);
for($i=0;$i<10;$i++)
	new Computer_Printer(get_computer_name('printer_',1,25), $intranet);

//----------------------------------------------------------------------------------------
//DEBUG!
//$home->addTool('cam');
//$home->addTool('wake');
//$info = '';
//foreach(get_defined_vars() as $k=>$v) if (is_string($v)) $info.= "$k=$v\n";
//$home->createFiles(array('info.txt'=>$info),'home/frank');
//$home->addNetworkInterface('eth1', $lab_outer);
//$home->addNetworkInterface('eth2', $intranet);

