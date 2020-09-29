<?php
namespace Library\Cipher;

class Encrypt
{
   
   public static function hash(int $length = 7)
   {
      $hex = ['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f'];
      $hash = "";
      for ($max = $length; $max > 0; $max--) {
         $i = rand(0,15);
         $hash .= $hex[$i];
      }

      return $hash;
   }

   public static function token(int $length = 6)
   {
      $token = "";
      for ($max = $length; $max > 0; $max--) {
         $token .= random_int(0,9);
      }

      return $token;
   }

   public static function digest(string $username, string $password)
   {
      // md5(username:realm:password)
      return \md5($username . ":" . DIGEST_REALM . ":" . $password);
   }

}