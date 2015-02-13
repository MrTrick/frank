<?
require_once 'Common.php';
   
class Frank_Game_TestCase extends Frank_Common_TestCase {
    protected $name = "Game";
    
    /**
     * Spoiler!
     * This test runs quickly through the game
     * as a user, eg searching in the right places etc to find info,     
     * performing all the actions that are necessary to complete the game.
     *
     * Don't read this function unless you want the whole thing spoiled.
     */  
    public function testGameSequence() {
        $email1 = $this->runCommandSequence(array(
            'ls /tmp/mail' => "Contents of /tmp/mail:<br />\n&nbsp;<span style='color:#ddf'>20110215.msg</span><br />\n&nbsp;<span style='color:#ddf'>20110303.msg</span><br />",
            'cat /tmp/mail/20110303.msg' => '&gt; -----Original Message'
        ));
        
        //Get the computer names from the email
        $this->assertTrue( !!preg_match("/\[(lab_\d+)\] <i>Project FRANK/", $email1->stdout, $m) );
        $lab_frank = $m[1];
        $this->assertTrue( !!preg_match("/\[(lab_\d+)\] <i>Quarantined file server/", $email1->stdout, $m) );
        $sec_server = $m[1];
        
        //Copy the necessary tools to the local computer
        $this->runCommandSequence(array(
            "ftp $sec_server" => "Connected to ftpd - File server on $sec_server - as anonymous<br />",
            "pwd" => "/ftp",
            "ls" => "Contents of /ftp:<br />\n&nbsp;<span style='color:#00f'>camera/</span><br />\n&nbsp;<span style='color:#00f'>isos/</span><br />\n&nbsp;<span style='color:#00f'>scratch/</span>",
            "get tools/wake" => "File transferred successfully to /home/frank/wake",
            "get tools/cam" => "File transferred successfully to /home/frank/cam",
            "quit" => "Bye!",
        ));
        
        //Connect to the camera and search for details
        $cam_view = $this->runCommandSequence(array(
            "./cam $sec_server" => "camd - Camera server on $sec_server. User: anonymous"
        ));
        $this->assertTrue( !!preg_match("/\| bob:(\w+)/", $cam_view->stdout, $m) );
        $bob_password = $m[1];
        $this->assertTrue( !!preg_match("/laptop = (\w+)/", $cam_view->stdout, $m) );
        $lab_laptop = $m[1];
        $this->runCommandSequence(array(
            'q'=>"Disconnected from $sec_server - bye."
        ));
        
        //Connect again as bob and find the wep key
        $cam_view = $this->runCommandSequence(array(
           "./cam $sec_server bob:$bob_password" => "camd - Camera server on $sec_server. User: bob",
           "a 100" => "+--------"
        ));
        $this->assertTrue( !!preg_match("/lab WEP key: (\w+)/", $cam_view->stdout, $m) );
        $wep_key = $m[1];
        $this->runCommandSequence(array(           
           'q'=>"Disconnected from $sec_server - bye."
        )); 
        
        //Wake up the laptop and get wifi access
        $this->runCommandSequence(array(
            "./wake $lab_laptop" => "$lab_laptop has woken, and is connected to LAB_SECURE",
            "ssh $lab_laptop bob:$bob_password" => "Connected to sshd - Secure shell server on $lab_laptop",
            "ifconfig wg0 start $wep_key" => "wg0 is now active",
        ));
        
        //Get seymour's password and go back to frank's computer
        $transcript = $this->runCommandSequence(array(
            "ssh lab_server bob:$bob_password" => "Connected to sshd - Secure shell server on lab_server",
            "cat /phone/transcripts/07.txt" => "#Outgoing: 0-41104928133",
        ));
        $this->assertTrue( !!preg_match("/the password is (\w+)/", $transcript->stdout, $m) );
        $seymour_password = $m[1];
        $this->runCommandSequence(array(
            "exit" => "Disconnected from lab_server"
        ), array(
            "exit" => "Disconnected from $lab_laptop"
        ));
        
        //Copy the needed files to the workstation
        $this->runCommandSequence(array(
            "ftp $lab_laptop bob:$bob_password" => "Connected to ftpd - File server on $lab_laptop - as bob",
            "put frank" => "File transferred successfully to /ftp/frank",
            "quit" => "Bye!",
            "ssh $lab_laptop bob:$bob_password" => "Connected to sshd - Secure shell server on $lab_laptop",
            "ftp lab_workstation_1 seymour:$seymour_password" => "Connected to ftpd - File server on lab_workstation_1 - as seymour",
            "put /ftp/frank" => "File transferred successfully to /ftp/frank",
            "put /bin/ftp" => "File transferred successfully to /ftp/ftp"
        ), array(
            "quit" => "Bye!",
        ));
        
        //Copy the marketing files from the corporate file server
        $this->runCommandSequence(array(        
            "ssh lab_workstation_1 seymour:$seymour_password" => "Connected to sshd - Secure shell server on lab_workstation_1",
            "ifconfig eth1 start" => "eth1 is now active",
            "/ftp/ftp aai_files" => "Connected to ftpd - File server on aai_files - as anonymous",
            "get Marketing" => "Folder transferred successfully to /home/seymour/Marketing",
            "quit" => "Bye!"          
        ));
        
        $finish = $this->runCommandSequence(array(
            "cp /home/seymour/Marketing/campaigns/2011/viral/mail_merge_copy.txt /tmp/message.txt" => "mail_merge_copy.txt copied",
            "cp /home/seymour/Marketing/customers/opt_in_list.txt /tmp/to_list.txt" => "opt_in_list.txt copied",            
            "cp /home/seymour/Marketing/tools/mailbomb /tmp/" => "mailbomb copied",
            "cp /ftp/frank /tmp/" => "frank copied",
            "cd /tmp" => "/tmp",
            "./mailbomb to_list.txt message.txt frank" => "Queued for sending... sent!<br />\n<span style='color:#ff0;'><i>This ought to be good...</i></span>"
        ));
        
        $this->assertObjectHasAttribute("game_over", $finish);
        $this->assertInternalType("string", $finish->game_over);
        
        //Game over! 
    }
 }
    
    
       
