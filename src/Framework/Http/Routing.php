<?php
namespace Framework\Http;

use Framework\Http\Request;
use Framework\Http\Response;

class Routing
{

   // TODO: check these patterns with real values
   private static $any_param_type = "/[0-9A-Za-z -_]+/";

   private static $digits_param_type  = "/[0-9]+/";                          //:d

   private static $letters_param_type = "/[A-Za-z]+/";                       //:a

   private static $letters_plus_param_type = "/[A-Za-z -_]+/";               //:a+

   private static $alpha_numeric_param_type = "/[0-9A-Za-z]+/";              //:x

   private static $alpha_numeric_plus_param_type = "/[0-9A-Za-z -_]+/";      //:x+


   public function __construct()
   {

   }

   public static function route(string $method, Request $request, Response $response)
   {
      // get the controller
      $controller = $method;
      // break the controller
      $break = explode('@', $controller);
      // Get the Controller Class and the Method
      $controllerName = $break[0];
      $methodName = $break[1];

      // include the controller's namespace
      $namespace = "Controllers\\";
      $controller = $namespace.$controllerName;

      // Create a new instance of the Controller
      if (class_exists($controller)) {
         $controller = new $controller();
         // if the requested method exists
         if (method_exists($controller, $methodName)){
            // send the result from the controller to the HttpResponse class to return response to the user
            // NB: httpParams would always be sent to a controller method
            $routed = true;
            $controller->$methodName($request, $response);
         }else{
            // handle error
            Error::notFound("Requested Controller Method <i><b>'$methodName'</b></i> not found in controller <i><b>'$controllerName'</b></i> <!--");
         }
      } else{
         // handle error
         Error::notFound("Requested Controller <i><b>'$controller'</b></i> not found <!--");
      }
   }

   /**
    * the route is same as uri when their chunks length are equal AND
    * there is similarity in their non-parameter chunks AND 
    * their parameter chunks are of the same type
    */
   public static function compare(string $route, string $uri)
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
            $route_param_pattern = self::$any_param_type;

            // get the real parameter and its type if it is set
            $chunk = explode(":",$route_param);
            // length can only be 1 or 2
            if (count($chunk) == 2) {
               // set the real route parameter
               $route_param = $chunk[0];
               // set the parameter type
               switch ($chunk[1]) {
                  case 'd':
                     $route_param_pattern = self::$digits_param_type;
                     break;
                  case 'a':
                     $route_param_pattern = self::$letters_param_type;
                     break;
                  case 'a+':
                     $route_param_pattern = self::$letters_plus_param_type;
                     break;
                  case 'x':
                     $route_param_pattern = self::$alpha_numeric_param_type;
                     break;
                  case 'x+':
                     $route_param_pattern = self::$alpha_numeric_plus_param_type;
                     break;
                  default:
                     $route_param_pattern = self::$any_param_type;
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

   public function __desctruct()
   {

   }

}

// {id} - true
// {id:} - false
// {id:a} - true
// {id:d} - true
// {id:x} - true
// {id:a+} - true
// {id:x+} - true
// {id:d+} - false
// {id:ad} - false
// {id:ax} - false
// {id:dd} - false
// {id:xd} - false
// {id:axd} - false

// $matches = [
   // "{id}",
   // "{id:}",
   // "{id:a}",
   // "{id:d}",
   // "{id:x}",
   // "{id:a+}",
   // "{id:x+}",
   // "{id:d+}",
   // "{id:ad}",
   // "{id:ax}",
   // "{id:dd}",
   // "{id:xd}",
   // "{id:axd}",
   // "{id:+}",
   // "{id:+a}",
   // "{:id}",
   // "{a:id}",
   // "{a+:id}"

   // for expr
   
// ];

// foreach ($matches as $pattern) {
//    grep($pattern);
//    // grep(trim($pattern,"{}"));
// }

// function preg($val) {
//    echo $val . " => ";
//    echo (preg_match("/{[0-9A-Za-z]+(|[:]([d]|[ax](|[+])))}/", $val)) ? "TRUE" : "FALSE";
//    echo "\n";
// }

// function grep($val) {
//    echo $val . " => ";
//    echo json_encode(preg_grep("/(|[:]([d]|[ax](|[+])))/",[$val]));
//    echo "\n";
// }

// [{]{2} *((?![{]{2})(?! +)(?![}]{2}).)+ *[}]{2}

// echo grep("Hello {{ name }} and {{ vic }} {{ ");

// function grep($val) {
//    // echo $val . " => ";
//    // echo json_encode(preg_grep("[{{](.)+ *[}}]/",[$val]));
//    // echo "\n";

//    $re = '/[{]{2} *((?![{]{2})(?! +)(?![}]{2}).)+ *[}]{2}/m';
//    // $str = 'my name is {{    j   }} {{ name }}
//    // {{ name }}';

//    preg_match_all($re, $val, $matches, PREG_SET_ORDER, 0);

//    // Print the entire match result
//    // echo json_encode($matches);

//    foreach ($matches as $match ) {
//       $rMatch = $match[0];
//       $val = \preg_replace("/$rMatch/", str_replace("{{", "<?=", str_replace("}}", "? >", $rMatch)), $val);
//       // $val = str_replace("{{", "<?=", str_replace("}}", "? >", $val));
//    }
//    return $val;
// }