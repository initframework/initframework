<?php
namespace Framework\Http;
use Framework\FrameworkException;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Http\Routing;
use Framework\Http\Auth;

class Http
{
   
   private $request;
   private $response;
   private $auth;

   private $uri;
   private $method;

   private $route_auth_type;
   private $route_guard_roles;

   public function __construct()
   {
      // TODO: prepare csrf token
      
      // Set all Configurations
      date_default_timezone_set(TIMEZONE);

      // start the session
      $lifetime = (SESSION_LIFETIME * 60);
      $path = '/';
      $domain = "localhost";
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

      // initiate new request, response and auth
      $this->request = new Request();
      $this->response = new Response();
      $this->auth = new Auth();

      // If application is on maintenance mode and client IP is not whitelisted
      // block the access
      if (MAINTENANCE == true && !in_array($this->request->ip(), MAINTENANCE_ALLOWED_IP)) {
         $this->response->send($this->response->render('framework/maintenance.html'), 403);
      }

      $this->method = $this->request->method();
      $this->uri = $this->request->uri();

      // default auth type
      $this->default_auth_type();
      
   }

   public function get(string $route, $next)
   {
      // restrict to only GET requests
      if ($this->method != "GET") {
         // default the auth type, incase any had been set before
         $this->default_auth_type();
         return;
      }
      
      $this->handle_web_request($route, $next);
   }

   public function post(string $route, $next)
   {
      // restrict to only POST requests
      if ($this->method != "POST") {
         // default the auth type, incase any had been set before
         $this->default_auth_type();
         return;
      }

      $this->handle_web_request($route, $next);
   }

   public function put(string $route, $next)
   {
      // restrict to only PUT requests
      if ($this->method != "PUT") {
         // default the auth type, incase any had been set before
         $this->default_auth_type();
         return;
      }

      $this->handle_web_request($route, $next);
   }

   public function patch(string $route, $next)
   {
      // restrict to only PATCH requests
      if ($this->method != "PATCH") {
         // default the auth type, incase any had been set before
         $this->default_auth_type();
         return;
      }

      $this->handle_web_request($route, $next);
   }

   public function delete(string $route, $next)
   {
      // restrict to only DELETE requests
      if ($this->method != "DELETE") {
         // default the auth type, incase any had been set before
         $this->default_auth_type();
         return;
      }

      $this->handle_web_request($route, $next);
   }






   // there would be another method called handle api request
   private function handle_web_request(string $route, $next)
   {
      
      // compare the uri with the route
      // get the status and the route parameters if declared in the route
      list($status, $route_params) = Routing::compare($route, $this->uri);

      // if comparison fails
      if ($status == false) {
         // default the auth type, incase any had been set before
         $this->default_auth_type();
         return;
      }
      
      // comparison pass
      // intercept the process to check that the user is authenticated
      if ($this->route_auth_type != "None") {
         $this->auth->check($this->request, $this->response, $this->route_auth_type);
      }

      // intercept the process to detect that the user has permissions to access this route
      if ($this->route_guard_roles != []) {
         $this->auth->guard($this->request, $this->response, $this->route_guard_roles);
      }

      // set the route parameter property of Request
      $this->request->set_route_params($route_params);

      // set a default response header for the request

      // check if next is a function, and execute it
      if (\is_callable($next)) {
         $next($this->request, $this->response);
      }
      // else if it is a string
      elseif (\is_string($next)) {
         // then call the controller method -> next
         Routing::route($next, $this->request, $this->response);
      }
   }






   private function default_auth_type()
   {
      $this->route_auth_type = "None";
      $this->route_guard_roles = [];
   }






   // register a middleware authentication if this route matches the uri
   // app_handles_failure allows the user application to handle authentication failure
   public function auth(string $auth)
   {
      // accepted auth types
      $valid_auth_types = [
         "None", "Session", "Basic", "Digest", "OAuth", "OAuth2", "JWT"
      ];

      if ($auth == "web") {
         $this->route_auth_type = AUTH_WEB;
      } elseif ($auth == "api") {
         $this->route_auth_type = AUTH_API;
      } else if (\in_array($auth, $valid_auth_types)) {
         $this->route_auth_type = $auth;
      }
      
      return $this;
   }





   
   public function guard(...$roles)
   {
      $this->route_guard_roles = $roles;

      return $this;
   }







   // only called when no route is found
   public function end()
   {
      $this->response->send($this->response->render('framework/404.html'), 200);
   }






   public function __desctruct()
   {
      $this->request = null;
   }






}