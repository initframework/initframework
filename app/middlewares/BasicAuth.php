<?php
namespace Middlewares;
use Library\Http\Request;
use Services\User;

class BasicAuth
{
   public function __construct(Request $request)
   {
      if (!property_exists($request, "phpAuthUser"))
      {
         // set the response header
         header("WWW-Authenticate: Basic realm=\"".APP_NAME."\"", true, 401);
         exit;
      }

      // retrieve the credentials
      $username = $request->phpAuthUser;
      $password = $request->phpAuthPw;

      /*
         Use the username to retrieve the password from the database.
         Your Model Code Goes Here
      */

      // Default
      // username = admin, password = admin

      // Comment this when you have your password from the database
      $db_username = "admin"; $db_password = "admin";

      // You can replace this with your own authentication code
      if ($username == $db_username && $password == $db_password) {

         // set the user credentials
         User::$isAuthenticated = true;
         User::$username = $db_username;

      } else {

         // set the response header
         header("WWW-Authenticate: Basic realm=\"".APP_NAME."\"", true, 401);
         exit;
         
      }
   }
}
