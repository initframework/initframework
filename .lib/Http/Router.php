<?php
namespace Library\Http;
use Library\Http\Request;
// use Library\Http\Response;

class Router
{
   private static $request;
   private static $get, $post, $patch, $put, $delete;

   public static function init(Request $request)
   {
      self::$request = $request;
   }

   public static function get(string $route, ...$handlers)
   {
      self::$get[self::formatRoute($route)] = $handlers;
   }

   public static function post(string $route, ...$handlers)
   {
      self::$post[self::formatRoute($route)] = $handlers;
   }

   public static function patch(string $route, ...$handlers)
   {
      self::$patch[self::formatRoute($route)] = $handlers;
   }

   public static function put(string $route, ...$handlers)
   {
      self::$put[self::formatRoute($route)] = $handlers;
   }

   public static function delete(string $route, ...$handlers)
   {
      self::$delete[self::formatRoute($route)] = $handlers;
   }

   /**
      * Removes trailing forward slashes from the right of the route.
      * @param route (string)
      */
   private static function formatRoute($route)
   {
      $result = rtrim($route, '/');
      if ($result === '')
      {
         return '/';
      }
      return $result;
   }

   /**
    * Resolves a route
    */
   public static function resolve()
   {
      $methodDictionary = self::${strtolower(self::$request->requestMethod)};
      $formatedRoute = self::formatRoute(self::$request->requestUri);

      foreach ($methodDictionary as $route => $methods) {
         list($status, $params) = self::compare($route, $formatedRoute);
         
         if ($status == false) {
            continue;
         } else {
            self::$request->routeParams = $params;
            foreach ($methods as $method) {
               call_user_func_array($method, [self::$request]);
            }
            exit;
         }
      }

      notFoundError(self::$request);
      return;
   }

   
   /**
    * the route is same as uri when their chunks length are equal AND
    * there is similarity in their non-parameter chunks AND 
    * their parameter chunks are of the same type
    */
   private static function compare(string $route, string $uri)
   {
      // make sure the route and the uri are not empty
      if (empty($route) || empty($uri)) {
         return [false,[]];
      }

      // break the url
      $route_chunks = explode("/", $route);
      $uri_chunks = explode("/", $uri);

      // make sure that both uri are of the same length
      if (count($route_chunks) != count($uri_chunks)) {
         return [false,[]];
      }

      // get the chunk length
      $chunk_length = count($route_chunks);

      return self::match_chunks($route_chunks, $uri_chunks, $chunk_length);

   }

   // chunks are match bit by bit
   private static function match_chunks(array $route_chunks, array $uri_chunks, int $chunk_length)
   {
      // route parameters
      $route_parameters = [];
      // assume truthy
      $match = true;

      // while match is true, the loop keeps matching;
      // hence, if one match fails, the loop breaks and the function returns false
      // i.e all match must be true to assert that the uri is equal to the route

      for ($i = 0; $i < $chunk_length && $match == true; $i++)
      {
         // bits of each chunk for matching
         $route_chunk = $route_chunks[$i];
         $uri_chunk = $uri_chunks[$i];

         // if the route chunk preg_matches { parameter }
         // parameter can decide to accept: digits, letters, alphanumeric, letters +, alphanumeric +
         // it means the route is expecting a parameter here
         if (preg_match("/{[0-9A-Za-z]+(|[:]([d]|[ax](|[+])))}/", $route_chunk)) {
            
            // extract the parameter and assume its type to be null
            // remove the curly braces
            $route_param = trim($route_chunk, "{}");
            $route_param_pattern = "/[0-9A-Za-z -_.]+/";

            // get the real parameter and its type if it is set
            $chunk = explode(":",$route_param);
            // length can only be 1 or 2
            if (count($chunk) == 2) {
               // set the real route parameter
               $route_param = $chunk[0];
               // set the parameter type
               switch ($chunk[1]) {
                  case 'd':
                     $route_param_pattern = "/[0-9]+/";
                     break;
                  case 'a':
                     $route_param_pattern = "/[A-Za-z]+/";
                     break;
                  case 'a+':
                     $route_param_pattern = "/[A-Za-z -_.]+/";
                     break;
                  case 'x':
                     $route_param_pattern = "/[0-9A-Za-z]+/";
                     break;
                  case 'x+':
                     $route_param_pattern = "/[0-9A-Za-z -_.]+/";
                     break;
                  default:
                     $route_param_pattern = "/[0-9A-Za-z -_.]+/";
                     break;
               }
            }

            // get the value of the parameter, which is the uri chunk
            $route_param_value = $uri_chunk;

            // validate the parameter type of the uri chunk
            if (preg_match($route_param_pattern, $route_param_value)) {
               // validation passed
               // assign value to param, assume a match and check the next chunk
               $route_parameters[$route_param] = $route_param_value;
               $match = true;
            } else {
               // validation failed
               // match false and break the loop
               $match = false;
            }

         }
         // and if it doesn't preg_match { parameter }
         // then this is a non-parameter chunk
         else {
            // compare both chunks for similarity
            if ($route_chunk == $uri_chunk) {
               // assign value to param, assume a match and check the next chunk
               $match = true;
            }else{
               // match false and break the loop
               $match = false;
            }
         }

      }

      // test match
      return [$match, $route_parameters];
   }

   public function __destruct()
   {
      self::resolve();
   }
}