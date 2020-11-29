<?php
namespace Library\Http;
use Library\Http\Cache;

class Request
{
   public function __construct()
   {
      if (PHP_SAPI == 'cli') {
         // Set Timezone
         date_default_timezone_set(TIMEZONE);
         $this->bootstrapConsole();
      } else {
         // Set Timezone
         date_default_timezone_set(TIMEZONE);
         $this->bootstrapApp();
      }

      // Do not cache
      /*
         // Get cached data if any exist for this route
         if (CACHE_REQUEST) {

            // Note: getallheaders() only work for Apache seervers
            $headers = getallheaders();
            
            // Check if the client is requesting a fresh response
            if (!array_key_exists('Cache-Control', $headers) || !in_array($headers['Cache-Control'], ['no-cache', 'no-store', 'must-revalidate'])){
               $cached = Cache::get($this->requestUri);
               if ($cached) {
                  $cachedData = $cached['data'];
                  $cachedContentType = $cached['contentType'];
                  $cachedCode = $cached['code'];
                  header("Content-Type: $cachedContentType", true, $cachedCode);
                  if ($cachedContentType == 'application/json; charset=UTF-8') {
                     exit(json_encode($cachedData));
                  } elseif ($cachedContentType == 'text/html; charset=UTF-8') {
                     exit($cachedData);
                  }
               }
            }
         }
      */

      // Set Session Directory
      session_save_path(SESSION_DIR);
      // Start Session
      $lifetime = (AUTH_LIFETIME * 60);
      $path = '/';
      $domain = ENV == 'dev' ? 'localhost' : SERVER ;
      $secure = (isset($this->serverPort) && $this->serverPort == "443") ? true : false ;
      $httponly = true;
      session_start(
         [
            'name' => SESSION_NAME,
            'sid_length' => 128,
            'cookie_lifetime' => $lifetime,
            'cookie_path' => $path,
            'cookie_domain' => $domain,
            'cookie_secure' => $secure,
            'cookie_httponly' => $httponly,
            'cookie_samesite' => 'Lax'
         ]
      );
      $_ENV['REQUEST'] = $this;
   }

   private function bootstrapApp()
   {
      foreach($_SERVER as $key => $value)
      {
         $this->{$this->toCamelCase($key)} = $value;
      }
      // uri
      $this->requestUri = preg_replace("/\?.+/m", "", $this->requestUri);
      
      // route params would be defined by the router
      $this->routeParams = null;
      // request query
      $this->query = $_GET;
      // request body
      if (in_array($this->requestMethod, ["POST", "PATCH", "PUT", "DELETE"])) {
         $this->body = $_POST;
      }
      // accomodating request methods from html forms (spoofing)
      if ($this->requestMethod == "POST" && isset($this->body['HTTP_REQUEST_METHOD']) && in_array($this->body['HTTP_REQUEST_METHOD'], ["PUT", "PATCH", "DELETE"]))
      {
         $this->requestMethod = $this->body['HTTP_REQUEST_METHOD'];
      }
   }

   private function bootstrapConsole()
   {
      if (isset($_SERVER['argc']) && $_SERVER['argc'] > 0) {
         $this->command = $_SERVER['argv'][1];
         unset($_SERVER['argv'][0]);
         unset($_SERVER['argv'][1]);
         $this->commandParams = [...$_SERVER['argv']];
      }
   }

   // function for converting array keys to their camelCase equivalent
   public function toCamelCase($string)
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
      foreach($this as $obj) {
         unset($obj);
      }
   }

}
