<?php
namespace Services;

class User
{
   public static $isAuthenticated = false;
   public static $username = '';
   public static $role = 'Guest';
   public static $privileges = [];

   public static function user()
   {
      return json_encode([
         "auth"=>self::$isAuthenticated,
         "username"=>self::$username,
         "role"=>self::$role,
      ]);
   }

   public static function has(string $privilege)
   {
      return in_array($privilege, self::$privileges);
   }

   public static function is(string $role)
   {
      return self::$role == $role;
   }

   public static function isNot(string $role)
   {
      return self::$role != $role;
   }

}