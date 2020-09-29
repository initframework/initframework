<?php
namespace App\Services;

class User
{

   public static $auth = false;
   public static $username = '';
   public static $role = '';

   public static function user()
   {
      return json_encode([
         "auth"=>self::$auth,
         "username"=>self::$username,
         "role"=>self::$role,
      ]);
   }

}