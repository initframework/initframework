<?php
namespace App;

class Error
{
   # 400
   public static function badRequest(string $errMsg)
   {
      // trigger error | return http response
      (ini_get('display_errors') == 1) ? trigger_error($errMsg, E_USER_WARNING) : \http_response_code(400);
      die();
   }

   # 401
   public static function unauthorized(string $errMsg)
   {
      // trigger error | return http response
      (ini_get('display_errors') == 1) ? trigger_error($errMsg, E_USER_WARNING) : \http_response_code(401);
      die();
   }

   # 403
   public static function forbidden(string $errMsg)
   {
      // trigger error | return http response
      (ini_get('display_errors') == 1) ? trigger_error($errMsg, E_USER_WARNING) : \http_response_code(403);
      die();
   }

   # 404
   public static function notFound(string $errMsg)
   {
      // trigger error | return http response
      (ini_get('display_errors') == 1) ? trigger_error($errMsg, E_USER_WARNING) : \http_response_code(404);
      die();
   }

   # 500
   public static function internalError(string $errMsg)
   {
      // trigger error | return http response
      (ini_get('display_errors') == 1) ? trigger_error($errMsg, E_USER_WARNING) : \http_response_code(500);
      die();
   }

}

?>