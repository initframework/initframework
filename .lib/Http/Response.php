<?php

function __validateCode($code) {
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

function success($message = "success", array $data = null, int $code = 200)
{
   __validateCode($code);
   header("Content-Type: application/json; charset=UTF-8", true, $code);
   exit(json_encode([
      "status" => true,
      "message" => $message,
      "data" => $data
   ]));
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
   if (substr_compare($req->httpAccept, "text/html", 0) == true) {
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
      exit(\file_get_contents($file));
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
      $view = \file_get_contents($file);
   } else {
      trigger_error("'$filename' does not exist in the template directory!");
   }
}

function asset(string $path) {
   return ASSETS_PATH . ltrim($path, '/');
}

function storage(string $path) {
   return STORAGE_PATH . ltrim($path, '/');
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

      // exit(json_encode($data));

      if (TEMPLATE_ENGINE == "init") {
         $viewData = "<?php ";
         foreach ($data as $key => $value) {
            $viewData .= "$$key = '$value';";
         }
         $viewData .= " ?>";
      } elseif (TEMPLATE_ENGINE == "mirror.js") {
         $viewData = "@vars";
         foreach ($data as $key => $value) {
            $viewData .= "$$key = '$value';";
         }
         $viewData .= "@@vars";
      }
   }

   // preload user instance
   // if (TEMPLATE_ENGINE == "init") {
      // $viewData .= "<? php \$user = json_decode('" . \App\Services\User::user() . "'); ? >";
   // } elseif (TEMPLATE_ENGINE == "mirror.js") {
      // $viewData .= "@var \$user = " . \App\Services\User::user() . ";";
   // }
   
   if (file_exists($file)) {
      // Convert PHP Snippets
      $view = \file_get_contents($file);
      // replace init snippets
      // @import
      $view = import($view);
      // @csrftoken
      // $view = preg_replace("/@csrftoken/", "<input type=\"hidden\" name=\"CSRFToken\" value=\"" . \App\Services\Auth::csrfToken() . "\"/>", $view);
      // request methods
      $view = preg_replace("/@methodPut/", "<input type=\"hidden\" name=\"HTTP_REQUEST_METHOD\" value=\"PUT\">", $view);
      $view = preg_replace("/@methodPatch/", "<input type=\"hidden\" name=\"HTTP_REQUEST_METHOD\" value=\"PATCH\">", $view);
      $view = preg_replace("/@methodDelete/", "<input type=\"hidden\" name=\"HTTP_REQUEST_METHOD\" value=\"DELETE\">", $view);
      // view parameters
      $view = preg_replace("/@vars/", $viewData, $view);
      
      if (TEMPLATE_ENGINE == "init")
      {
         // directives
         // if-auth
         // $re = '/@if-auth */m';
         // preg_match_all($re, $view, $matches, PREG_SET_ORDER, 0);
         // foreach ($matches as $match ) {
         //    $rMatch = $match[0];
         //    $view = \preg_replace("/$rMatch/", "< ?php if (\App\Services\User::\$auth == true) { ? >", $view);
         // }
         // // if-not-auth
         // $re = '/@if-not-auth */m';
         // preg_match_all($re, $view, $matches, PREG_SET_ORDER, 0);
         // foreach ($matches as $match ) {
         //    $rMatch = $match[0];
         //    $view = \preg_replace("/$rMatch/", "< ?php if (\App\Services\User::\$auth != true) { ? >", $view);
         // }

         // opening tags
         $re = '/@(if|for|foreach|while) *[(]((?!@+).)+[)] */m';
         preg_match_all($re, $view, $matches, PREG_SET_ORDER, 0);
         foreach ($matches as $match ) {
            $rMatch = $match[0];
            $rMatch = str_replace("$","\\$", $rMatch);
            $rMatch = str_replace("(","\\(", $rMatch);
            $rMatch = str_replace(")","\\)", $rMatch);
            $rMatch = str_replace("[","\\[", $rMatch);
            $rMatch = str_replace("]","\\]", $rMatch);
            $view = \preg_replace("/$rMatch/", str_replace("@", "<?php ", $match[0] . " { ?>"), $view);
         }
         // middle tags
         $re = '/@elseif *[(]((?!@+).)+[)] */m';
         preg_match_all($re, $view, $matches, PREG_SET_ORDER, 0);
         foreach ($matches as $match ) {
            $rMatch = $match[0];
            $rMatch = str_replace("$","\\$", $rMatch);
            $rMatch = str_replace("(","\\(", $rMatch);
            $rMatch = str_replace(")","\\)", $rMatch);
            $rMatch = str_replace("[","\\[", $rMatch);
            $rMatch = str_replace("]","\\]", $rMatch);
            $view = \preg_replace("/$rMatch/", str_replace("@", "<?php } ", $match[0] . " { ?>"), $view);
         }
         $re = '/@else */m';
         $view = \preg_replace($re, "<?php } else { ?>", $view);
         // closing tags
         $re = '/@(endif|endforeach|endfor|endwhile) */m';
         $view = \preg_replace($re, "<?php } ?>", $view);
         // php
         // $re = '/@php((?!@+).)+;+/m';
         $re = '/@php((?!@+).)+;/m';
         preg_match_all($re, $view, $matches, PREG_SET_ORDER, 0);
         foreach ($matches as $match ) {
            $rMatch = $match[0];
            $rMatch = preg_quote($rMatch, "/");

            $view = \preg_replace("/$rMatch/", str_replace("@php", "<?php ", $match[0] . " ?>"), $view);
         }
         // expressions
         // $re = '/[{]{2} *((?![{]{2})(?! +)(?![}]{2}).)+ *[}]{2}/m';
         $re = '/[{]{2} *((?![{]{2})(?![}]{2}).)+ *[}]{2}/m';
         preg_match_all($re, $view, $matches, PREG_SET_ORDER, 0);
         // exit(json_encode($matches));
         foreach ($matches as $match ) {
            if (trim($match[0], '{} ') != "" ) {
               $rMatch = trim($match[0]);
               $rMatch = preg_quote($rMatch, "/");

               $view = \preg_replace("/$rMatch/", str_replace("{{", "<?=", str_replace("}}", "?>", $match[0])), $view);
            }
         }
         
         try {
            ob_start();
            eval("?>" . $view . "<?php");
            $renderedView = ob_get_contents();
            ob_end_clean();
         } catch (ParseError $e) {
            echo "&raquo; Error parsing view <b><i>$filename</i></b> on line <b><i>" . $e->getLine() . "</i></b><br>";
            echo "<textarea style='width: 100%; height: 25em;' disabled>";
            $count = 1; $lines = explode("\r\n", $view);
            foreach($lines as $line) {
               echo $count == $e->getLine() 
                  ? str_pad("🚩  ", strlen(count($lines)), "0", STR_PAD_LEFT) 
                  : str_pad($count, strlen(count($lines)), "0", STR_PAD_LEFT) . ") ";
               echo $line . "\r\n";
               $count++;
            }
            echo "</textarea><br><br>" . PHP_EOL;
            trigger_error($e->getMessage());
         }
         
         exit($renderedView . PHP_EOL);

      } elseif (TEMPLATE_ENGINE == "mirror.js") {
         exit($view . PHP_EOL);
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

// redirection
function redirect(string $location)
{
   header("Location: $location");
   http_response_code(302);
}

// authentication headers
// function auth_basic($realm)
// {
//    // set the response header
//    remove_all_headers();
//    add_header('WWW-Authenticate', sprintf('Basic realm="%s"', $realm));
//    send('Access Denied', 401);
// }

   // function auth_digest($realm)
   // {
   //    // generate authentication parameters
   //    // nonce, to make each request unique
   //    $nonce = md5(uniqid("", true));
   //    // opaque, must be returned by the client unaltered
   //    $opaque = md5(uniqid());
   //    // qop, the quality of protection of the request
   //    $qop = "auth";
   //    // set the response header
   //    remove_all_headers();
   //    add_header('WWW-Authenticate', sprintf('Digest realm="%s", qop="%s", nonce="%s", opaque="%s"', $realm, $qop, $nonce, $opaque));
   //    send('Access Denied', 401);
   // }

   // function auth_oauth()
   // {

   // }

   // function auth_oauth2()
   // {

   // }

   // function auth_jwt()
   // {

   // }

// }
