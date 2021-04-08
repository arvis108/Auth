<?php

class FirstTest extends \PHPUnit\Framework\TestCase{
   public function testUsername()
    {
        $this->assert();
        $result = (!preg_match("/^[a-zA-Z0-9_.-]*$/", 'username'));
        $result::assertEqual(TRUE,$result);
    }
}