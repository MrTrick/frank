<?
require_once 'Common.php';

class Frank_Auto_TestCase extends Frank_Common_TestCase {
    protected $name = "Autocomplete";
    
   /**
     * Utility - assert that two arrays contain the same elements
     * @param unknown $expected
     * @param unknown $actual
     * @param string $message
     */
    public function assertEqualSets($expected, $actual, $message='') {
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual, $message);
    }

    protected function runAutoSequence($_part_responses) {
       foreach(func_get_args() as $part_responses)
       foreach($part_responses as $part=>$expected) {
          $res = $this->session->autocomplete($part);
          $this->assertEquals($expected['suggest'], $res->suggest, "Autocomplete for '$part'");
          $this->assertEqualSets($expected['choices'], $res->choices, "Autocomplete for '$part'");
       }
       return $res; //Return the output of the last command
    }
    
    public function testCommands() { 
       $ALL = array('ls','cd','pwd','cat','ps','find','cp','mv','rm','mkdir',
                    'user','hostname','ping','ifconfig','ftp','ssh','su','help');
       $TOP = array('bin/','boot/','dev/','etc/','home/','media/','root/','tmp/','ftp/','tut/');
    
       $this->runAutoSequence(array(
          '' => array('suggest'=>'', 'choices'=>$ALL),
          'l' => array('suggest'=>'s', 'choices'=>array('ls')),
          'ls' => array('suggest'=>'', 'choices'=>array('ls')),
          'c' => array('suggest'=>'', 'choices'=>array('cp','cd','cat')),
          '/' => array('suggest'=>'', 'choices'=>$TOP),
          '/tmp' => array('suggest'=>'/', 'choices'=>array('tmp/')),
          '/tmp/' => array('suggest'=>'', 'choices'=>array('mail/','hello')),
          './' => array('suggest'=>'frank', 'choices'=>array('frank'))
       ));
    }
    
    public function testFiles() {
        $this->runAutoSequence(array(
          'cat ' => array('suggest'=>'', 'choices'=>array('crash.log','frank','notes.txt')),
          'cat n' => array('suggest'=>'otes.txt', 'choices'=>array('notes.txt')),
          'cat /tmp/' => array('suggest'=>'', 'choices'=>array('mail/', 'hello', 'test.txt')),
          'cat /tmp/m' => array('suggest'=>'ail/', 'choices'=>array('mail/')),
          'cat /tmp/mail/' => array('suggest'=>'20110', 'choices'=>array('20110303.msg', '20110215.msg')),
        ));
    }

 }
