<?php
namespace Middlewares;
use Library\Http\Request;
use Library\Http\Router;
use Services\User;

class SessionAuth
{
   public function __construct(Request $request)
   {

      // retrieve credentials
      $username = $_SESSION['username'] ?? null;
      // $role = $_SESSION['role'] ?? null;
      // $privileges = $_SESSION['privileges'] ?? null;

      // these are credentials coming from the session
      if ( !is_null($username)
      //  && !is_null($role) && !is_null($privileges) 
      ) {
         // set the users details
         User::$isAuthenticated = true;
         User::$username = $username;
         User::$privileges = ['dashboard','settings'];
      } else {
         redirect(Router::getRoute('login') ?? '/');
      }
   }
}
