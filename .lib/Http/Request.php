<?php
namespace Library\Http;

class Request
{
   public function __construct()
   {
      $this->bootstrapApp();
   }

   private function bootstrapApp()
   {
      // Bootstrap CLI
      $this->bootstrapCli();

      foreach($_SERVER as $key => $value)
      {
         $this->{$this->toCamelCase($key)} = $value;
         // echo "$key     $value<br>";
      }
      $this->requestUri = preg_replace("/\?.+/m", "", $this->requestUri);
      // route params would be defined by the router
      $this->routeParams = null;
      // request query
      $this->query = $_GET;
      // request body
      if (in_array($this->requestMethod, ["POST", "PATCH", "PUT", "DELETE"])) {
         foreach($_POST as $key => $value)
         {
            $this->body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
         }
      }
   }

   private function bootstrapCli()
   {
      // $this->bootstrapLocalServer();
      // foreach ($_SERVER as $key => $value) {
      //    echo "$key => $value<br>";
      // }
      // exit;
      // if (isset($_SERVER['argc']) && $_SERVER['argc'] > 0) {
      //    $params = $_SERVER['argv'][1];

      //    list($method, $route) = explode("?", $params);
      //    $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
      //    $_SERVER['REQUEST_METHOD'] = $method;
      //    $_SERVER['REQUEST_URI'] = $route;
      // } else {
      //    $url = $_SERVER['REQUEST_SCHEME'] ?? "http" . "://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
      //    // exit($url);
      //    $_SERVER['REQUEST_URI'] = str_replace(SERVER, "", $url);
      // }
   }

   private function toCamelCase($string)
   {
      $result = strtolower($string);
         
      preg_match_all('/_[a-z]/', $result, $matches);

      foreach($matches[0] as $match)
      {
         $c = str_replace('_', '', strtoupper($match));
         $result = str_replace($match, $c, $result);
      }

      return $result;
   }

   public function __desctruct()
   {
      // unset($this);
   }

}
