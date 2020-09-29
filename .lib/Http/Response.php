<?php
namespace Library\Http;
use Library\Handler\IException;

class Response
{

   private $response_content_type;

   public function __construct()
   {
      $this->remove_all_headers();
   }

   public function html(string $filename)
   {
      try {
         // set content type to html
         $this->add_header("Content-Type", "text/html; charset=UTF-8", true);
         
         $view = ""; $viewData = "";
         $file = TEMPLATE_DIR . $filename;

         // if file exists
         if (file_exists($file)) {
            // Convert PHP Snippets
            return $view = \file_get_contents($file);
         } else {
            throw new IException("Error: File $filename does not exist!");
         }
      } catch(IException $ex) {
         $ex->handle();
      }
   }

   public function xml(string $filename)
   {
      try {
         // set content type to xml
         $this->add_header("Content-Type", "application/xml; charset=UTF-8", true);
         
         $view = ""; $viewData = "";
         $file = TEMPLATE_DIR . $filename;

         // if file exists
         if (file_exists($file)) {
            // Convert PHP Snippets
            $view = \file_get_contents($file);
         } else {
            throw new IException("Error: File $filename does not exist!");
         }
      } catch(IException $ex) {
         $ex->handle();
      }
   }

   public function json(array $data = [], $options = 0, $depth = 512)
   {
      // set content type to json
      $this->add_header("Content-Type", "application/json; charset=UTF-8", true);
      return json_encode($data, $options, $depth);
   }

   private function import(string $view)
   {
      try {
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
                  throw new IException("File: $file does not exist in the template directory!");
               }
               $count++;
            }
            return $this->import($view);
         } else {
            return $view;
         }
      } catch (IException $ex) {
         $ex->handle();
      }
   }

   public function render(string $filename, array $data = null)
   {
      try {
         // set content type to html
         $this->add_header("Content-Type", "text/html; charset=UTF-8", true);

         $view = ""; $viewData = "";
         $file = TEMPLATE_DIR . $filename;

         // render data
         if ($data != null) {
      
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
         if (TEMPLATE_ENGINE == "init") {
            $viewData .= "<?php \$user = json_decode('" . \App\Services\User::user() . "'); ?>";
         } elseif (TEMPLATE_ENGINE == "mirror.js") {
            $viewData .= "@var \$user = " . \App\Services\User::user() . ";";
         }
         
         if (file_exists($file)) {
            // Convert PHP Snippets
            $view = \file_get_contents($file);
            // replace init snippets
            // @import
            $view = $this->import($view);
            // @csrftoken
            $view = preg_replace("/@csrftoken/", "<input type=\"hidden\" name=\"CSRFToken\" value=\"" . \App\Services\Auth::csrfToken() . "\"/>", $view);
            // @assets
            $view = preg_replace("/@assets\//", ASSETS_PATH, $view);
            // @storage
            $view = preg_replace("/@storage\//", STORAGE_PATH, $view);
            // request methods
            $view = preg_replace("/@method=PUT/", "<input type=\"hidden\" name=\"REQUEST_METHOD\" value=\"PUT\">", $view);
            $view = preg_replace("/@method=PATCH/", "<input type=\"hidden\" name=\"REQUEST_METHOD\" value=\"PATCH\">", $view);
            $view = preg_replace("/@method=DELETE/", "<input type=\"hidden\" name=\"REQUEST_METHOD\" value=\"DELETE\">", $view);
            // view parameters
            $view = preg_replace("/@vars/", $viewData, $view);
            
            if (TEMPLATE_ENGINE == "init")
            {
               // directives
               // if-auth
               $re = '/@if-auth */m';
               preg_match_all($re, $view, $matches, PREG_SET_ORDER, 0);
               foreach ($matches as $match ) {
                  $rMatch = $match[0];
                  $view = \preg_replace("/$rMatch/", "<?php if (\App\Services\User::\$auth == true) { ?>", $view);
               }
               // if-not-auth
               $re = '/@if-not-auth */m';
               preg_match_all($re, $view, $matches, PREG_SET_ORDER, 0);
               foreach ($matches as $match ) {
                  $rMatch = $match[0];
                  $view = \preg_replace("/$rMatch/", "<?php if (\App\Services\User::\$auth != true) { ?>", $view);
               }
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
                  $rMatch = str_replace("$","\\$", $rMatch);
                  $rMatch = str_replace("(","\\(", $rMatch);
                  $rMatch = str_replace(")","\\)", $rMatch);
                  $rMatch = str_replace("[","\\[", $rMatch);
                  $rMatch = str_replace("]","\\]", $rMatch);
                  $view = \preg_replace("/$rMatch/", str_replace("@php", "<?php ", $match[0] . " ?>"), $view);
               }
               // expressions
               // $re = '/[{]{2} *((?![{]{2})(?! +)(?![}]{2}).)+ *[}]{2}/m';
               $re = '/[{]{2} *((?![{]{2})(?![}]{2}).)+ *[}]{2}/m';
               preg_match_all($re, $view, $matches, PREG_SET_ORDER, 0);
               foreach ($matches as $match ) {
                  if (trim($match[0], '{} ') != "" ) {
                     $rMatch = trim($match[0]);
                     $rMatch = str_replace("$","\\$", $rMatch);
                     $rMatch = str_replace("(","\\(", $rMatch);
                     $rMatch = str_replace(")","\\)", $rMatch);
                     $rMatch = str_replace("[","\\[", $rMatch);
                     $rMatch = str_replace("]","\\]", $rMatch);
                     $rMatch = str_replace("?","\\?", $rMatch);
                     $view = \preg_replace("/$rMatch/", str_replace("{{", "<?=", str_replace("}}", "?>", $match[0])), $view);
                  }
               }
               
               ob_start();
               eval("?>" . $view . "<?php");
               $view = ob_get_contents();
               ob_end_clean();
               return $view;

            } elseif (TEMPLATE_ENGINE == "mirror.js") {
               return $view;
            }
         } else {
            throw new IException("Error: File $filename does not exist!");
         }
      } catch(IException $ex) {
         $ex->handle();
      }
   }

   public $response_sent = false;
   public function send(string $body = "", int $status = 200)
   {
      // Content from http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
      $http_status = [
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
         // -----
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
      ];

      try {
         if (in_array($status, $http_status)) {
            // set the response code
            \http_response_code($status);

            $this->response_sent = true;
            // send the response body
            exit($body);
         } else {
            throw new IException("Error: Invalid Response Code - $status");
         }
      } catch (IException $ex) {
         $ex->handle();
      }
      
   }
   
   // header control
   public function remove_all_headers()
   {
      if (!\headers_sent()) {
         // echo "No header sent";
         \header_remove();
      }
   }

   public function remove_header(string $header)
   {
      if (!\headers_sent()) {
         \header_remove($header);
      }
   }

   public function remove_headers(array $headers)
   {
      if (!\headers_sent()) {
         foreach ($headers as $header) {
            header_remove($header);
         }
      }
   }

   public function add_header(string $header, string $value, bool $replace = true)
   {
      if (!\headers_sent()) {
         header(sprintf('%s: %s', $header, $value), $replace);
      }
   }

   public function add_headers(array $headers, bool $replace = true)
   {
      if (!\headers_sent()) {
         foreach ($headers as $header => $value) {
            \header(\sprintf('%s: %s', $header, $value), $replace);
         }
      }
   }

   // redirection
   public function redirect(string $location)
   {
      $this->add_header("Location", $location);
      $this->send("", 302);
   }

   // authentication headers
   public function auth_basic($realm)
   {
      // set the response header
      $this->remove_all_headers();
      $this->add_header('WWW-Authenticate', sprintf('Basic realm="%s"', $realm));
      $this->send('Access Denied', 401);
   }

   public function auth_digest($realm)
   {
      // generate authentication parameters
      // nonce, to make each request unique
      $nonce = md5(uniqid("", true));
      // opaque, must be returned by the client unaltered
      $opaque = md5(uniqid());
      // qop, the quality of protection of the request
      $qop = "auth";
      // set the response header
      $this->remove_all_headers();
      $this->add_header('WWW-Authenticate', sprintf('Digest realm="%s", qop="%s", nonce="%s", opaque="%s"', $realm, $qop, $nonce, $opaque));
      $this->send('Access Denied', 401);
   }

   public function auth_oauth()
   {

   }

   public function auth_oauth2()
   {

   }

   public function auth_jwt()
   {

   }

}