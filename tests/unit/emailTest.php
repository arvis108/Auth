<?php

class FirstTest extends \PHPUnit\Framework\TestCase{
   public function testUsername()
    {
        $result = false;
        if(preg_match("/^[a-zA-Z0-9_.-]*$/", 'username')){
            $result = true;
        } 
        $this->assertTrue($result);
    }
    public function testPwd()
    {
        $myFile = "./includes/pwd-list.txt";
        $fh = fopen($myFile, "r");
        if ($fh) {
        while ( !feof($fh) ) {
            $passwords[] = trim(fgets($fh));    
            }
        }
        $result = (in_array('password', $passwords,FALSE));
        $this->assertTrue($result);
    }
}