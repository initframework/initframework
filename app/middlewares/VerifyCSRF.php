<?php
namespace Middlewares;
use Library\Http\Request;
use Library\Http\Router;
use Services\Cipher;

class VerifyCSRF
{
   public function __construct(Request $request)
   {
      $csrftoken = $request->body['CSRFToken'] ?? $request->httpCsrftoken ?? null;

      if ( is_null($csrftoken) || $csrftoken == '' || Cipher::decryptAES(APP_KEY, $csrftoken) != APP_NAME) {
         redirect(Router::getRoute('login') ?? "/");
      }
   }
}
