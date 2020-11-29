<?php
namespace Middlewares;
use Library\Http\Request;
use Library\Http\Router;
use Services\User;
use Services\JWT;

class JWTAuth
{
   public function __construct(Request $request)
   {
      $token = $request->body['JWT'] ?? $request->httpJwt ?? null;

      if ( is_null($token) || $token == '') {
         redirect(Router::getRoute('login') ?? "/");
      }

      try {
         $payload = JWT::decode($token, APP_KEY, ['HS256', 'HS384', 'HS512', 'RS256']);
         
         // retrieve credentials
         User::$isAuthenticated = true;
         User::$username = $payload->username;

         // set the users details

      } catch (\Exception $e) {
         redirect(Router::getRoute('login') ?? '/');
      }
   }
}
