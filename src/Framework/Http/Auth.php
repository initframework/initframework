<?php
namespace Framework\Http;
use App\Auth as AppAuth;
use OAuth;
use OAuthProvider;
use OAuthException;

class Auth
{

   private $app_auth;

   public function __construct() {
      $this->app_auth = new AppAuth();
   }

   // this method would only be called when the request uri matches the route
   // returns true if success, and exits if failure
   public function check(Request $request, Response $response, string $route_auth_type)
   {

      // LOGIC
      // this route expects your request to come with the basic auth type and nothing else
      // if your request came with a diff auth type => you would be requested to authenticate with the right auth type.
      // if your request came in with the basic auth type, validate the credentials
      // if the credentials are valid => return true
      // else => (request re-authentication| send bad request) and return false

      switch ($route_auth_type) {
         
         // the default for web applications on the browser
         case 'Session':
            
            if ($request->auth_type() != "Session") {

               // request a Session Authentication from the client via login form
               $response->remove_all_headers();
               $response->redirect('login');

            } else {

               return $this->app_auth->auth_session($request, $response);

            }

         break;

         // set of authentication types for APIs
         case 'Basic':

            if ($request->auth_type() != "Basic") {

               // request a Basic Authentication from the client
               $response->auth_basic(BASIC_REALM);

            } else {

               // validate the username and password is correct.
               if ($this->app_auth->auth_basic($request, $response) == true) {
                  // success
                  return true;
               }
               // request a valid Basic Authentication from the client
               // or send bad request
               else {
                  $response->auth_basic(BASIC_REALM);
               }

            }
            
         break;

         case 'Digest':

            if ($request->auth_type() != "Digest") {
               
               // request a Digest Authentication from the client
               $response->auth_digest(DIGEST_REALM);

            } else {

               // validate the username and password is correct.
               if ( $this->app_auth->auth_digest($request, $response, $request->auth_credentials()) ) {
                  // success
                  return true;
               }
               // request a valid Basic Authentication from the client
               // or send bad request
               else {
                  $response->auth_digest(DIGEST_REALM);
               }

            }

         break;

         case 'OAuth':
            return true;
         break;
               
         case 'OAuth2':
            return true ;
         break;
         
         // also valid for applications on the web browser
         case 'JWT':
            return true;
         break;

         // default when no auth type is indicated
         case 'None':
            return true;
         break;

         default:
            // None
            return true;
         break;

      }

   }

   public function guard(Request $request, Response $response, $route_guard_roles)
   {
      $this->app_auth->guard($request, $response, $route_guard_roles);
   }

   public static function login(array $credentials, bool $remember)
   {
      // consider remember
      if ($remember) {

         // should we use the remember_token in the db
         // so that i can set a cookie (remember) here
         // then we use it in the check method.

         // OR

         // should we extend the session time
         // and add a remember field
         // so it can be tested for later

         // i would use the second option
         $remember_credential = [
            "remember" => $remember
         ];

         // adding the remember to the credentials is now useless, we just increase the remember me time, lets try it on edge

         $credentials = array_merge($credentials, $remember_credential);

         // extend the lifetime
         $lifetime = (REMEMBER_ME_LIFETIME * 60);
         session_set_cookie_params($lifetime);

      }
      
      $_SESSION['AUTH'] = $credentials;
      
      // as best practice, regenerate session id
      \session_regenerate_id();

   }

   public static function logout()
   {
      \session_unset();
      \session_destroy();
   }

}