<?php
namespace Library\Http;
use Library\Handler\IException;
use Library\Http\Routing;
use Library\Http\Middleware;

class Http extends Middleware
{
   
   private $uri;
   private $method;
   private $httpMethods = array(
      "GET",
      "POST",
      "PATCH",
      "PUT",
      "DELETE"
   );

   public function __construct()
   {
      parent::__construct();
      $this->method = $this->request->method();
      $this->uri = $this->request->uri();
   }

   function __call($name, $args)
   {
      list($route, $method) = $args;

      if(!in_array(strtoupper($name), $this->httpMethods))
      {
         $this->invalidMethodHandler();
      }

      $this->{strtolower($name)}[$this->formatRoute($route)] = $method;
   }

   /**
    * Resolves a route
    */
   function resolve()
   {
      $methodDictionary = $this->{strtolower($this->request->requestMethod)};
      $formatedRoute = $this->formatRoute($this->request->requestUri);
      $method = $methodDictionary[$formatedRoute];

      if(is_null($method))
      {
      $this->defaultRequestHandler();
      return;
      }

      echo call_user_func_array($method, array($this->request));
   }

   function __destruct()
   {
      $this->resolve();
   }

   // public function get(string $route, $next)
   // {
   //    // restrict to only GET requests
   //    if ($this->method != "GET") {
   //       $this->reset();
   //       return;
   //    }
      
   //    $this->handle_request($route, $next);
   // }

   // public function post(string $route, $next)
   // {
   //    // restrict to only POST requests
   //    if ($this->method != "POST") {
   //       $this->reset();
   //       return;
   //    }

   //    $this->handle_request($route, $next);
   // }

   // public function put(string $route, $next)
   // {
   //    // restrict to only PUT requests
   //    if ($this->method != "PUT") {
   //       $this->reset();
   //       return;
   //    }

   //    $this->handle_request($route, $next);
   // }

   // public function patch(string $route, $next)
   // {
   //    // restrict to only PATCH requests
   //    if ($this->method != "PATCH") {
   //       $this->reset();
   //       return;
   //    }

   //    $this->handle_request($route, $next);
   // }

   // public function delete(string $route, $next)
   // {
   //    // restrict to only DELETE requests
   //    if ($this->method != "DELETE") {
   //       $this->reset();
   //       return;
   //    }

   //    $this->handle_request($route, $next);
   // }

   // private function handle_request(string $route, $next)
   // {
      
   //    // compare the uri with the route
   //    // get the status and the route parameters if declared in the route
   //    list($status, $route_params) = Routing::compare($route, $this->uri);

   //    // if comparison fails
   //    if ($status == false) {
   //       $this->reset();
   //       return;
   //    }

   //    // middleware check
   //    $this->check();
      
   //    // set the route parameter property of Request
   //    $this->request->set_route_params($route_params);

   //    // check if next is a function, and execute it
   //    if (\is_callable($next)) {
   //       $next($this->request, $this->response);
   //    }
   //    // else if it is a string
   //    elseif (\is_string($next)) {
   //       // then call the controller method -> next
   //       Routing::route($next, $this->request, $this->response);
   //    }
   // }

   // public function auth(string $auth)
   // {
   //    $this->auth = $auth;
   //    return $this;
   // }

   // public function guard(...$roles)
   // {
   //    $this->roles = $roles;
   //    return $this;
   // }

   // public function ip_allow(...$ips)
   // {
   //    $this->ips = $ips;
   //    return $this;
   // }

   // public function csrf()
   // {
   //    $this->antiCsrf = true;
   //    return $this;
   // }

   // public function end()
   // {
   //    // if (!$this->response->response_sent) {
   //       $this->response->send($this->response->render('framework/404.html'), 404);
   //    // }
   // }
   
}