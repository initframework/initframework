<?php
use PHPUnit\Framework\TestCase;
use Services\Validate;

class ValidateTest extends TestCase
{

   public function testHasExactLength()
   {
      Validate::hasExactLength('field', 'abcde', 4);
      $this->assertNotEmpty(Validate::$error);
      $this->assertNotTrue(Validate::$status);
   }

}