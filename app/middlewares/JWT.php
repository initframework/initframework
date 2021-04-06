<?php
namespace Middlewares;
use Library\Http\Request;
// use Library\Http\Router;
use Services\User;
use Services\JWT as JWTService;
use Services\Cipher;

class JWT
{
   public static function auth(Request $request)
   {
      $token = $request->httpAuthtoken ?? '';

      if ( empty($token) == true) {
         error('Please login', null, 401);
      }

      // try {
         
         // retrieve credentials
         $ciphertext = JWTService::decode($token, APP_KEY, ['HS256', 'HS384', 'HS512', 'RS256']);         
         $payload = json_decode(Cipher::decryptAES(APP_KEY, $ciphertext));

         // set the users details
         User::$isAuthenticated = true;
         User::$id = $payload->id;
         User::$email = $payload->email;
         User::$role = $payload->role;
         User::$privileges = explode(",", $payload->permissions);
         
      // } catch (\Exception $e) {
      //    error('Please login', null, 401);
      // }
   }
   
   public static function groupAuth(Request $request, $callback)
   {
       if (1 == 1) {
           $callback();
       }
   }
   
}
