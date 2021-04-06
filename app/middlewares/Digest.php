<?php
namespace Middlewares;
use Library\Http\Request;
use Services\Cipher;
use Services\User;

class Digest
{
   public static function auth(Request $request)
   {
      
      if (!property_exists($request, "phpAuthDigest"))
      {
         $nonce = md5(uniqid("", true));
         // opaque, must be returned by the client unaltered
         $opaque = md5(uniqid());
         // qop, the quality of protection of the request
         $qop = "auth";
         // set the response header
         header(sprintf('WWW-Authenticate: Digest realm="%s", qop="%s", nonce="%s", opaque="%s"', APP_NAME, $qop, $nonce, $opaque), true, 401);
         exit;
      }

      // get the auth credentials
      $credentials = Cipher::decryptDigest($request->phpAuthDigest);

      /*
         Your Model Code Goes Here
      */

      // Default
      // username = admin, password = admin, realm = Initframework
      // the database password is bbb4bde70be5f36e293e3f7f9e76cafa
      
      // Comment this when you have your password from the database
      $password = "bbb4bde70be5f36e293e3f7f9e76cafa";

      $username = $credentials['username'];
      $nonce = $credentials['nonce'];
      $nc = $credentials['nc'];
      $cnonce = $credentials['cnonce'];
      $qop = $credentials['qop'];
      $uri = $credentials['uri'];
      $request_response = $credentials['response'];

      $A1 = $password;
      $A2 = md5($request->requestMethod . ":$uri");
      $valid_response = md5("$A1:$nonce:$nc:$cnonce:$qop:$A2");

      if ($request_response == $valid_response) {
         
         // set the user credentials
         User::$isAuthenticated = true;
         User::$email = $username;
         
      } else {
         
         $nonce = md5(uniqid("", true));
         // opaque, must be returned by the client unaltered
         $opaque = md5(uniqid());
         // qop, the quality of protection of the request
         $qop = "auth";
         // set the response header
         header(sprintf('WWW-Authenticate: Digest realm="%s", qop="%s", nonce="%s", opaque="%s"', APP_NAME, $qop, $nonce, $opaque), true, 401);
         exit;

      }
   }
}
