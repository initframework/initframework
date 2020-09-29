<?php
namespace App\Services;
use Library\Http\Request;
use Library\Http\Response;
use Library\Database\Model;
use Library\Handler\IException;
use App\Services\User;
use Library\Cipher\Encrypt;
use Library\Cipher\AES;

class Auth
{

   // web login
   public static function session_login(Request $request, Response $response)
   {

      $username = trim($request->body()->username);
      $password = trim($request->body()->password);
      $remember = isset($request->body()->remember) ? true : false ;

      if (!empty($username) && !empty($password)) {

         if ($username == "admin" && $password == "admin") {

            // set user credentials
            // Note: this credentials were used to authenticate the user in the ath_session method above
            $credentials = [
               "username" => $username,
               "role" => "admin",
               "privileges" => "*"
            ];

            // consider remember
            if ($remember) {
               // extend the session lifetime
               $lifetime = (REMEMBER_ME_LIFETIME * 60);
               session_set_cookie_params($lifetime);
            }

            // add auth to session
            $_SESSION['AUTH'] = $credentials;
            // as best practice, regenerate session id
            \session_regenerate_id();

            // set the users details
            User::$auth = true;
            User::$username = $credentials['username'];
            User::$role = $credentials['role'];

            return true;

         } else {
            // $response->remove_all_headers();
            // $response->redirect("login");
            // die("Only Admin");
            return false;
         }

      } else {
         // $response->remove_all_headers();
         // $response->redirect("login");
         // die("Username & Password is empty");
         return false;
      }

   }

   // web logout
   public static function session_logout(Request $request, Response $response)
   {
      \session_unset();
      \session_destroy();
      echo 'requesting redirect';
      $response->redirect("login");
   }
   

   // Auths

   // Session Auth
   public static function session(Request $request, Response $response)
   {
      
      // retrieve credentials
      $username = $request->auth_credentials()->username;
      $role = $request->auth_credentials()->role;
      $privileges = $request->auth_credentials()->privileges;

      // these are credentials coming from the session
      if ( isset($username) && !empty($username) && isset($role) && !empty($role) && isset($privileges) && !empty($privileges) ) {
         // set the users details
         User::$auth = true;
         User::$username = $username;
         User::$role = $role;
      } else {
         $response->remove_all_headers();
         $response->redirect("login", 401);
      }
      
   }

   // Basic Auth
   public static function basic(Request $request, Response $response)
   {
      // retrieve the credentials
      $username = $request->auth_credentials()->username;
      $password = $request->auth_credentials()->password;

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

      } else {
         $response->remove_all_headers();
         $response->auth_basic(BASIC_REALM);
      }

   }

   // Digest Auth
   public static function digest(Request $request, Response $response)
   {
      
      $username = $request->auth_credentials()->username;

      // use the username to retrieve the password (A1) from the database.
      // the password should be computed as md5(username:realm:actual-password)

      /*
         Your Model Code Goes Here
      */

      // Default
      // username = admin, password = admin, realm = Initframework
      // the database password is 330902e4da960d4a7fd25c09c41ebb8c
      
      // Comment this when you have your password from the database
      $password = "330902e4da960d4a7fd25c09c41ebb8c";

      // $realm = $credentials->realm;
      $nonce = $credentials->nonce;
      $nc = $credentials->nc;
      $cnonce = $credentials->cnonce;
      $qop = $credentials->qop;
      $uri = $credentials->uri;
      $request_response = $credentials->response;

      $A1 = $password;
      $A2 = md5($request->method() . ":$uri");
      $valid_response = md5("$A1:$nonce:$nc:$cnonce:$qop:$A2");

      if ($request_response == $valid_response) {
         
         return true;
      } else {
         $response->remove_all_headers();
         $response->auth_digest(DIGEST_REALM);
         return false;
      }
      
   }

   // JWT Auth
   public static function jwt()
   {

   }

   // OAuth Auth
   public static function oauth()
   {

   }

   // OAuth2 Auth
   public static function oauth2()
   {

   }

   // ---------------------

   // public static function digest_password(string $username, string $password)
   // {
   //    Encrypt::digest($username, $password);
   // }

   // ---------------------


   // Middleware Handlers
   // Guard
   public static function guard(Request $request, Response $response, $roles)
   {
      $username = $request->auth_credentials()->username;
      $role = $request->auth_credentials()->role;
      $privileges = $request->auth_credentials()->privileges;

      if (!\in_array($role, $roles)) {
         $response->remove_all_headers();
         $response->redirect($request->referer());
      }
   }

   // Ip
   public static function ip_allow(Request $request, Response $response, $ips)
   {
      $ip = $request->ip();
      if (!\in_array($ip, $ips)) {
         $response->remove_all_headers();
         $response->redirect($request->referer());
      }
   }

   // Anti CSRF
   public static function antiCsrf(Request $request, Response $response)
   {
      try {
         if ( $request->csrftoken() == '' 
         || AES::decrypt(SECRET_KEY, $request->csrftoken()) != session_id()) {
            // log possible csrf attack
            // throw new IException("SECURITY: Possible CSRF attack from IP: " . $request->ip());
            // log user out
            self::session_logout($request, $response);
         }
      } catch (IException $ex) {
         $ex->handle();
      }
      
   }

   // Generating CSRF Token for View
   public static function csrfToken()
   {
      // return AES::encrypt(SECRET_KEY, session_id() . time());
      return AES::encrypt( SECRET_KEY, session_id() );
   }

}
