<?php
namespace Framework\Http;
use Framework\Http\Request;
use Framework\Http\Response;
use App\Services\Auth;
// use OAuth;
// use OAuthProvider;
// use OAuthException;

class Middleware
{

   public $request;
   public $response;
   public $auth = '';
   public $roles = [];
   public $ips = [];
   public $antiCsrf = false;

   public function __construct() {
      $this->setup();
      $this->request = new Request();
      $this->response = new Response();
      $this->maintenance();
   }

   private function setup()
   {
      // Set Timezone
      date_default_timezone_set(TIMEZONE);

      // Start Session
      $lifetime = (SESSION_LIFETIME * 60);
      $path = '/';
      $domain = SERVER == DEV_SERVER ? 'localhost' : SERVER ;
      $secure = ($_SERVER['REQUEST_SCHEME'] == "http") ? false : true ;
      $httponly = true;

      session_start(
         [
            'name' => SESSION_NAME,
            'sid_length' => 225,
            'cookie_lifetime' => $lifetime,
            'cookie_path' => $path,
            'cookie_domain' => $domain,
            'cookie_secure' => $secure,
            'cookie_httponly' => $httponly,
            'cookie_samesite' => 'Lax'
         ]
      );
   }

   private function maintenance()
   {
      // If application is on maintenance mode and client IP is not whitelisted
      // block the access
      if (MAINTENANCE == true && !in_array($this->request->ip(), MAINTENANCE_ALLOWED_IP)) {
         $this->response->send($this->response->render('framework/maintenance.html'), 403);
      }
   }

   public function check()
   {
      if ($this->auth != ''){
         $this->auth($this->auth);
      }
      if ($this->roles != []) {
         $this->guard($this->roles);
      }
      if ($this->ips != []) {
         $this->ip_allow($this->ips);
      }
      if ($this->antiCsrf == true) {
         $this->antiCsrf();
      }
   }

   public function reset() {
      $this->auth = '';
      $this->roles = [];
      $this->ips = [];
      $this->antiCsrf = false;
   }
   
   private function auth(string $type)
   {
      $valid_auth_types = [
         "Session", "Basic", "Digest", "OAuth", "OAuth2", "JWT"
      ];

      if ($type == "web") {
         $type = AUTH_WEB;
      } elseif ($type == "api") {
         $type = AUTH_API;
      } elseif (\in_array($type, $valid_auth_types)) {
         $type = $type;
      } else {
         return $this;
      }
      
      // LOGIC
      // if request auth type is diff from route auth type, request auth with route auth type
      // if request auth type is same with route auth type, verify credentials
      // if credentials are not correct, request auth with route auth type
      // return type: terminates request on failure or void on success

      switch ($type) {
         
         case 'Session':
            if ($this->request->auth_type() != "Session") {
               // request a Session Authentication from the client via login form
               $this->response->remove_all_headers();
               $this->response->redirect('login');
            } else {
               Auth::session($this->request, $this->response);
            }
         break;

         case 'Basic':
            if ($this->request->auth_type() != "Basic") {
               // request a Basic Authentication from the client
               $this->response->auth_basic(BASIC_REALM);
            } else {
               // validate the username and password is correct.
               Auth::basic($this->request, $this->response);
            }
         break;

         case 'Digest':
            if ($this->request->auth_type() != "Digest") {
               // request a Digest Authentication from the client
               $this->response->auth_digest(DIGEST_REALM);
            } else {
               // validate the username and password is correct.
               Auth::digest($this->request, $this->response);
            }
         break;

         case 'OAuth':
            // return true;
         break;
               
         case 'OAuth2':
            // return true ;
         break;
         
         case 'JWT':
            // return true;
         break;

         default:
            // return true;
         break;

      }

      return;
   }

   private function guard(...$roles)
   {
      // return type: terminates request on failure or void on success
      Auth::guard($this->request, $this->response, $roles[0]);
      return;
   }

   private function ip_allow(...$ips)
   {
      // return type: terminates request on failure or void on success
      Auth::ip_allow($this->request, $this->response, $ips[0]);
      return;
   }

   private function antiCsrf()
   {
      Auth::antiCsrf($this->request, $this->response);
      return;
   }

   private function throttle()
   { }

}