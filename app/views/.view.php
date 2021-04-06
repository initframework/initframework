<?php

/**
 * Write functions that can be called in the view files
 */

// function for turning arrays to objects
function objectify(array $array) : object
{
   if (is_array($array) && $array !== []) {
      $obj = new \stdClass();
      foreach ($array as $key => $value) {
         // convert the value into an object if value is also an array
         if (is_array($value)) {
            $value = objectify($value);
         }
         // assign the value to the key
         $key = toCamelCase($key);
         $obj->$key = $value;
      }

      $value = null;
      return $obj;
   }

   return null;
}

// function for converting array keys to their camelCase equivalent
function toCamelCase($string)
{
   $result = strtolower($string);
      
   preg_match_all('/_[a-z]/', $result, $matches);

   foreach($matches[0] as $match)
   {
      $c = str_replace('_', '', strtoupper($match));
      $result = str_replace($match, $c, $result);
   }

   return $result;
}

function asset(string $path)
{
   return ASSETS_PATH . ltrim($path, '/');
}

function storage(string $path)
{
   return STORAGE_PATH . ltrim($path, '/');
}

function UserIsAuthenticated()
{
   return \Services\User::$isAuthenticated;
}

function UserHasPrivilege($privilege)
{
   return \Services\User::has($privilege);
}

function UserRoleIs($role)
{
   return \Services\User::is($role);
}

function UserRoleIsNot($role)
{
   return \Services\User::isNot($role);
}