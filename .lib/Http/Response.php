<?php

function __validateCode($code)
{
   // Content from http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
   if (!in_array($code, [
      // Information response codes
      100, // Continue
      101, // Switching Protocols
      103, // Processing // WebDAV; RFC 2518

      // Success response codes
      200, // Ok
      201, // Created
      202, // Accepted
      203, // Non-Authoritative Information
      204, // No Content
      205, // Reset Content
      206, // Partial Content
      // 207, // Multi-Status // WebDAV; RFC 4918
      // 208, // IM Used // WebDAV; RFC 5842
      // 226, // IM Used // RFC 3229

      // Redirect response codes
      300, // Multiple Choices
      301, // Moved Permanently
      302, // Found
      303, // See Other
      304, // Not Modified
      306, // Switch Proxy
      307, // Temporary Redirect
      308, // Permanent Redirect // approved as experimental RFC
      // -----
      // 305, // Use Proxy

      // Client response codes
      400, // Bad Request
      401, // Unauthorized
      402, // Payment Required
      403, // Forbidden
      404, // Not Found
      405, // Method Not Allowed
      406, // Not Acceptable
      407, // Proxy Authentication Required
      408, // Request Timeout
      409, // Conflict
      410, // Gone
      411, // Length Required
      412, // Precondition Failed
      413, // Request Entity Too Large
      414, // Request-URI Too Long
      415, // Unsupported Media Type
      416, // Requested Range Not Satisfiable
      417, // Expectation Failed
      // -----
      // 418, // I'm a teapot // RFC 2324
      // 419, // Authentication Timeout Redirect // not in RFC 2616
      // 420, // Enhance Your Calm // Twitter
      // 420, // Method Failure // Spring Framework
      // 422, // Unprocessable Entity // WebDAV; RFC 4918
      // 423, // Locked // WebDAV; RFC 4918
      // 424, // Failed Dependency // WebDAV; RFC 4918
      // 424, // Method Failure // WebDAV
      // 425, // Unordered Collection // Internet draft
      // 426, // Upgrade Required // RFC 2817
      // 428, // Precondition Required // RFC 6585
      // 429, // Too Many Requests // RFC 6585
      // 431, // Request Header Fields Too Large // RFC 6585
      // 444, // No Response // Nginx
      // 449, // Retry With // Microsoft
      // 450, // Blocked by Windows Parental Controls // Microsoft
      // 451, // Redirect // Microsoft
      // 451, // Unavailable For Legal Reasons // Internet draft
      // 494, // Request Header Too Large // Nginx
      // 495, // Cert Error // Nginx
      // 496, // No Cert // Nginx
      // 497, // HTTP to HTTPS // Nginx
      // 499, // Client Closed Request // Nginx

      // Server response codes
      500, // Internal Server Error
      501, // Not Implemented
      502, // Bad Gateway
      503, // Service Unavailable
      504, // Gateway Timeout
      505, // HTTP Version Not Supported
      511, // Network Authentication Required // RFC 6585
      // ------
      // 506, // Variant Also Negotiates // RFC 2295
      // 507, // Insufficient Storage // WebDAV; RFC 4918
      // 508, // Loop Detected // WebDAV; RFC 5842
      // 509, // Bandwidth Limit Exceeded // Apache bw/limited extension
      // 510, // Not Extended // RFC 2774
      // 598, // Network read timeout error // Unknown
      // 599, // Network connect timeout error // Unknown
   ])) trigger_error("Invalid Response Code");
   else return;
}

function __responseCache($contentType, $data, $code)
{
   // // Set cached data if any exist for this route
   // if (CACHE_REQUEST) {
   //    // Note: getallheaders() only work for Apache seervers
   //    $headers = getallheaders();
      
   //    // Check if the client is requesting a fresh response
   //    if (!array_key_exists('Cache-Control', $headers) || !in_array($headers['Cache-Control'], ['no-cache', 'no-store', 'must-revalidate'])){
   //       $req = $_ENV['REQUEST'];
   //       // cache indication
   //       if (!empty(strstr($contentType, 'application/json'))){
   //          // using json
   //          $data['caching'] = true;
   //       } elseif (!empty(strstr($contentType, 'text/html'))){
   //          // using html
   //          $data = "<!-- Using cached data -->\r\n" . $data;
   //       }
   //       $cache = ['code' => $code, 'data' => $data, 'contentType' => $contentType];
   //       \Library\Http\Cache::set($req->requestUri, $cache);
   //    }
   // }
}

function success($message = "success", array $data = null, int $code = 200)
{
   __validateCode($code);
   header("Content-Type: application/json; charset=UTF-8", true, $code);
   $data = [
      "status" => true,
      "message" => $message,
      "data" => $data
   ];
   // __responseCache("application/json; charset=UTF-8", $data, $code);
   exit(json_encode($data));
}

function error($message = "error", array $data = null, int $code = 400)
{
   __validateCode($code);
   header("Content-Type: application/json; charset=UTF-8", true, $code);
   exit(json_encode([
      "status" => true,
      "message" => $message,
      "data" => $data
   ]));
}

function notFoundError($req)
{
   if (substr_compare($req->httpAccept, "text/html", 0) == true) {
      render('framework/error.html', [
         "code" => 404,
         "message" => "Not Found"
      ], 404);
   } else {
      http_response_code(404);
      exit("Not Found");
      // http_throttle()
   }
}

function serverError($req)
{
   if (substr_compare($req->httpAccept, "text/html", 0) == true ) {
      render('framework/error.html', [
         "code" => 500,
         "message" => "Server Error"
      ], 500);
   } else {
      http_response_code(500);
      exit("Server Error");
      // http_throttle()
   }
}

function html(string $filename, int $code = 200)
{
   __validateCode($code);
   // set content type to html
   header("Content-Type: text/html; charset=UTF-8", true, $code);
   
   $file = TEMPLATE_DIR . $filename;

   // if file exists
   if (file_exists($file)) {
      $data = \file_get_contents($file);
      // __responseCache("text/html; charset=UTF-8", $data, $code);
      exit($data);
   } else {
      trigger_error("'$filename' does not exist in the template directory!");
   }
}

function xml(string $filename, int $code = 200)
{
   __validateCode($code);
   // set content type to xml
   header("Content-Type: application/xml; charset=UTF-8", true, $code);
   
   $file = TEMPLATE_DIR . $filename;

   // if file exists
   if (file_exists($file)) {
      $data = \file_get_contents($file);
      // __responseCache("application/xml; charset=UTF-8", $data, $code);
      exit($data);
   } else {
      trigger_error("'$filename' does not exist in the template directory!");
   }
}

function render(string $filename, array $data = null, int $code = 200)
{
   __validateCode($code);
   
   // set content type to html
   header("Content-Type: text/html; charset=UTF-8", true, $code);

   $view = ""; $viewData = "";
   $file = TEMPLATE_DIR . $filename;

   // render data
   if ($data != null) {
      $viewData = "<?php ";
      foreach ($data as $key => $value) {
         $viewData .= "$$key = '$value';";
      }
      $viewData .= " ?>";
   }
   
   if (file_exists($file)) {
      // Convert PHP Snippets
      $view = \file_get_contents($file);
      $view = implode(" \r", explode("\r", $view));
      // replace init snippets
      // @import
      $view = import($view);
      // @csrftoken
      $view = preg_replace("/@_csrftoken/", "<input type=\"hidden\" name=\"CSRFToken\" value=\"" . \Services\Cipher::encryptAES(APP_KEY, APP_NAME) . "\"/>", $view);
      $view = preg_replace("/@csrftoken/", \Services\Cipher::encryptAES(APP_KEY, APP_NAME), $view);
      // request methods
      $view = preg_replace("/@_method_put/", "<input type=\"hidden\" name=\"HTTP_REQUEST_METHOD\" value=\"PUT\">", $view);
      $view = preg_replace("/@_method_patch/", "<input type=\"hidden\" name=\"HTTP_REQUEST_METHOD\" value=\"PATCH\">", $view);
      $view = preg_replace("/@_method_delete/", "<input type=\"hidden\" name=\"HTTP_REQUEST_METHOD\" value=\"DELETE\">", $view);
      // view parameters
      $view = preg_replace("/@vars/", $viewData, $view);

      // directives

      // opening tags
      $re = '/@(if|for|foreach|while) *[(]((?!@+).)+[)] */m';
      preg_match_all($re, $view, $matches, PREG_SET_ORDER, 0);
      foreach ($matches as $match ) {
         $rMatch = $match[0];
         $rMatch = preg_quote($rMatch, '/');
         $view = \preg_replace("/$rMatch/", str_replace("@", "<?php ", $match[0] . " { ?>"), $view);
      }
      // middle tags
      $re = '/@elseif *[(]((?!@+).)+[)] */m';
      preg_match_all($re, $view, $matches, PREG_SET_ORDER, 0);
      foreach ($matches as $match ) {
         $rMatch = $match[0];
         $rMatch = preg_quote($rMatch, '/');
         $view = \preg_replace("/$rMatch/", str_replace("@", "<?php } ", $match[0] . " { ?>"), $view);
      }
      $re = '/@else */m';
      $view = \preg_replace($re, "<?php } else { ?>", $view);
      // closing tags
      $re = '/@(endif|endforeach|endfor|endwhile) */m';
      $view = \preg_replace($re, "<?php } ?>", $view);
      // php
      $re = '/@php((?!@+).)+;/m';
      preg_match_all($re, $view, $matches, PREG_SET_ORDER, 0);
      foreach ($matches as $match ) {
         $rMatch = $match[0];
         $rMatch = preg_quote($rMatch, "/");
         $view = \preg_replace("/$rMatch/", str_replace("@php", "<?php ", $match[0] . " ?>"), $view);
      }
      // expressions
      $re = '/[{]{2} *((?![{]{2})(?![}]{2}).)+ *[}]{2}/m';
      preg_match_all($re, $view, $matches, PREG_SET_ORDER, 0);
      // exit(json_encode($matches));
      foreach ($matches as $match ) {
         if (trim($match[0], '{} ') != "" ) {
            $rMatch = trim($match[0], ' ');
            $rMatch = preg_quote($rMatch, "/");
            $view = \preg_replace("/$rMatch/", str_replace("{{", "<?=", str_replace("}}", "?>", $match[0])), $view);
         }
      }
      
      try {
         ob_start();
         include_once(TEMPLATE_DIR . '.view.php');
         eval("?>" . $view . "<?php");
         $renderedView = ob_get_contents();
         ob_end_clean();
         
         $data = $renderedView . PHP_EOL;
         // __responseCache("text/html; charset=UTF-8", $data, $code);
         exit($data);

      } catch (Throwable $e) {
         echo "&raquo; Error parsing view <b><i>$filename</i></b> on line <b><i>" . $e->getLine() . "</i></b><br>";
         echo "<textarea style='width: 100%; height: 25em;' disabled>";
         $count = 1; $lines = explode("\r\n", $view); // \file_get_contents($file)
         foreach($lines as $line) {
            echo $count == $e->getLine() 
               ? str_pad("🔴  ", strlen(count($lines)), "0", STR_PAD_LEFT) 
               : str_pad($count, strlen(count($lines)), "0", STR_PAD_LEFT) . ") ";
            echo $line . "\r\n";
            $count++;
         }
         echo "</textarea><br><br>" . PHP_EOL;
         trigger_error($e->getMessage());
      }
      
   } else {
      trigger_error("'$filename' does not exist in the template directory!");
   }
}

function import(string $view)
{
   $catch = preg_match_all("/@import [A-Za-z0-9\/_.-]+\.html/", $view, $matches);
   if ($catch > 0) {
      $count = 0;
      foreach ($matches[0] as $match) {
         // replace / match with \/ in match for escaping directories
         $re_match = str_replace('/', '\/', $match);
         // identify the match
         $view = preg_replace("/$re_match/", "@import $count", $view);
         $match = explode("@import ", $match);
         // extract required file
         $file = $match[1];
         // if file exists, get content
         if (file_exists(TEMPLATE_DIR . $file)) {
            $contents = file_get_contents(TEMPLATE_DIR . $file);
            $view = preg_replace("/@import $count/", $contents, $view);
         } else {
            $view = preg_replace("/@import $count/", "/$match/",$view);
            trigger_error("'$view' does not exist in the template directory!");
         }
         $count++;
      }
      return import($view);
   } else {
      return $view;
   }
}

function route(string $name)
{
   return \Library\Http\Router::getRoute($name);
}

// redirection
function redirect(string $location)
{
   header("Location: $location");
   http_response_code(302);
}