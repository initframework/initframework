<?php
namespace Middlewares;
use Library\Http\Request;
use Library\Http\Router;
use Services\User;

class Guard
{
   public static function has(string $privilege)
   {
      if (!User::has($privilege)) error('Access denied', null, 403);
   }

   public static function is(string $role)
   {
      if (!User::is($role)) error('Access denied', null, 403);
   }

   public static function isAny(array $roles)
   {
      if (!User::isAny($roles)) error('Access denied', null, 403);
   }

   public static function hasAll(array $privileges)
   {
      if (!User::hasAll($privileges)) error('Access denied', null, 403);
   }

   public static function hasAny(array $privileges)
   {
      if (!User::hasAny($privileges)) error('Access denied', null, 403);
   }
}
