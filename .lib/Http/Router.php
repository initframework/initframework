<?php
namespace Library\Http;
use Library\Http\Request;
use function call_user_method;
class Router
{
   private static $request;
   private static $get, $post, $patch, $put, $delete, $allRoutes;
   private static $instance, $latestRoute;

   public static function start(Request $request)
   {
      self::$request = $request;
      self::$instance = new self();
      self::$get = []; self::$post = []; self::$patch = [];
      self::$put = []; self::$delete = []; self::$allRoutes = [];
   }

   public static function get(string $route, ...$handlers)
   {
      self::$get[] = [
         'route' => self::formatRoute($route),
         'handlers' => $handlers,
         'name' => null
      ];
      self::$latestRoute = 'get';
      return self::$instance;
   }

   public static function post(string $route, ...$handlers)
   {
      self::$post[] = [
         'route' => self::formatRoute($route),
         'handlers' => $handlers,
         'name' => null
      ];
      self::$latestRoute = 'post';
      return self::$instance;
   }

   public static function patch(string $route, ...$handlers)
   {
      self::$patch[] = [
         'route' => self::formatRoute($route),
         'handlers' => $handlers,
         'name' => null
      ];
      self::$latestRoute = 'patch';
      return self::$instance;
   }

   public static function put(string $route, ...$handlers)
   {
      self::$put[] = [
         'route' => self::formatRoute($route),
         'handlers' => $handlers,
         'name' => null
      ];
      self::$latestRoute = 'put';
      return self::$instance;
   }

   public static function delete(string $route, ...$handlers)
   {
      self::$delete[] = [
         'route' => self::formatRoute($route),
         'handlers' => $handlers,
         'name' => null
      ];
      self::$latestRoute = 'delete';
      return self::$instance;
   }

   public static function command(string $command, $handler)
   {
      if ($command == self::$request->command) {
         if (is_callable($handler)) {
            \call_user_func_array($handler, [self::$request->commandParams]);
         }
      }
   }

   public static function name(string $name)
   {
      switch (self::$latestRoute) {
         case 'get':
            self::$get[count(self::$get) - 1]['name'] = $name;
            break;
         case 'post':
            self::$post[count(self::$post) - 1]['name'] = $name;
            break;
         case 'put':
            self::$put[count(self::$put) - 1]['name'] = $name;
            break;
         case 'patch':
            self::$patch[count(self::$patch) - 1]['name'] = $name;
            break;
         case 'delete':
            self::$delete[count(self::$delete) - 1]['name'] = $name;
            break;         
         default:
            break;
      }
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
   private static function resolve()
   {
      self::$allRoutes = array_merge(self::$get ?? [], self::$post ?? [], self::$delete ?? [], self::$patch ?? [], self::$put ?? []);
      $methodDictionary = self::${strtolower(self::$request->requestMethod)};
      $formatedRoute = self::formatRoute(self::$request->requestUri);

      foreach ($methodDictionary as $url) {
         $route = $url['route'];
         $handlers = $url['handlers'];
         list($status, $params) = self::compare($route, $formatedRoute);
         
         if ($status == false) {
            continue;
         } else {
            self::$request->routeParams = $params;
            foreach ($handlers as $handler) {
               if (is_callable($handler)) {
                  \call_user_func_array($handler, [self::$request]);
                  break;
               }
            }
            exit;
         }
      }

      notFoundError(self::$request);
   }

   public static function getRoute(string $name)
   {
      foreach (self::$allRoutes as $route) {
         if ($route['name'] == $name) {
            return ltrim($route['route'], "/");
            break;
         }
      }
      return ltrim($name, "/");
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

   /**
    * This method returns the route url to the view
    *
    * @param string $name
    * @param array $urlParams
    * @return string $routeUrl
    */
   /*
      public static function getUri(string $name, array $urlParams = null)
      {
         if ( isset($name) ) {
            $domain = \Config["DOMAIN"];
            // url to be returned
            $routeUrl = "";

            foreach (self::$allRoutes as $route ) {
               if ( $route['name'] == $name ) {

                  // get the url
                  $uri = $route['uri'];

                  // if urlParams is set, then
                  if (isset($urlParams)) {
                     // break the url
                     $break = explode("/", $uri);
                     // assign the matched url parameter placeholder to an array
                     $got = preg_grep("/{[0-9a-zA-Z_+-@#]+}/", $break);

                     foreach ($got as $key => $value) {
                        // strip the curly braces and get the urlParam array position
                        $value = str_replace("{", "", str_replace("}", "", $value));
                        // replace any forward slash with an underscore (_) character
                        $urlParams[$value] = str_replace("/","_",$urlParams[$value]);
                        // assign the value of the urlparam to the broken url
                        $break[$key] = $urlParams[$value];
                     }

                     // implode the url together
                     $uri = implode("/", $break);
                  }
                  
                  $routeUrl = $domain . $uri;
                  return $routeUrl;
               }
            }
         }
      }
   */

   public function __destruct()
   {
      if (PHP_SAPI != 'cli') {
         self::resolve();
      }
   }
}