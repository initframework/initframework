<?php
namespace Library\Http;

class Request
{
   
   private $request;

   private $ip;

   private $server_ip;

   private $csrftoken;

   private $path;

   private $scheme;

   private $host;

   private $query;

   private $uri;

   private $method;

   private $body;

   private $files;

   private $params;

   private $protocol;

   private $referer;

   private $cookies;

   private $session;

   // Auth related properties
   private $auth_type;

   private $auth_credentials;


   public function __construct()
   {
      // get the request data
      $this->request = $_SERVER;

      // get the ip address
      $this->get_ip();

      // get the request scheme
      $this->get_scheme();

      // get the request host
      $this->get_host();

      // get the url path
      $this->get_path();

      // get the request method
      $this->get_method();

      // get the request query parameters
      $this->get_query();

      // get the request body
      $this->get_body();

      // get csrf token if any was sent
      $this->get_csrftoken();

      // get the request uri
      $this->get_uri();

      // get the files submitted
      $this->get_files();

      // get the request http protocol
      $this->get_protocol();

      // get the request referer
      $this->get_referer();

      // get the cookies that came with the request
      $this->get_cookies();

      // // get the session that came with the request
      // $this->get_session();

      // accomodating request methods from html
      $this->get_html_request_methods();

      // get authentication type
      $this->get_auth_type();
   }

   // --------------------------------------------------------- //

   private function get_ip()
   {
      $this->ip = $this->request['REMOTE_ADDR'];
      $this->server_ip = $this->request['SERVER_ADDR'];
   }

   private function get_path()
   {
      /* 
         ----------------------------------------------------------------
         Setting the request path
         ----------------------------------------------------------------

         The request path is a combination of the request scheme, the 
         hostname and the request uri which also contains the query 
         string.

         ----------------------------------------------------------------
      */

      $uri = $this->request['REQUEST_URI'];
      $this->path = urldecode($this->scheme . "://" . $this->host . $uri);
   }

   private function get_host()
   {
      $this->host = $this->request['SERVER_NAME'];
   }

   private function get_method()
   {
      $this->method = $this->request['REQUEST_METHOD'];
   }

   private function get_scheme()
   {
      /* 
         ----------------------------------------------------------------
         Setting the request scheme
         ----------------------------------------------------------------

         The request scheme is the part before the :// in the url.
         The most common request schemes are http and https.

         ----------------------------------------------------------------
      */
      $this->scheme = $this->request['REQUEST_SCHEME'];
   }

   private function get_uri()
   {
      /* 
         ----------------------------------------------------------------
         Setting the real request uri
         ----------------------------------------------------------------

         When on localhost, the exact request uri cannot be retrieved.
         Hence we retrieve it by replacing the APP_URL (from Config.php) 
         in the full request path with an empty string; and also replacing
         the query string with an empty string.

         ----------------------------------------------------------------
      */

      $uri = str_replace(SERVER, "", $this->path);
      
      // get the query string
      $queryString = $this->request['QUERY_STRING'];

      $this->uri = str_replace("?" . $queryString, "", $uri);
   }

   private function get_query()
   {
      /* 
         ----------------------------------------------------------------
         Setting the query parameters
         ----------------------------------------------------------------

         The query parameters can be gotten from the query string or from 
         the _GET global variable.
         It is then converted into an object.

         ----------------------------------------------------------------
      */

      $this->query = $this->objectify($_GET ?? []);
   }

   private function get_body()
   {
      $this->body = $this->objectify($_POST ?? []);
   }

   private function get_csrftoken()
   {
      $this->csrftoken = $this->body != [] ? $this->body->CSRFToken ?? '' : '' ;
   }
   
   private function get_files()
   {
      $this->files = $this->objectify($_FILES ?? []);
   }

   private function get_protocol()
   {
      $this->protocol = $this->request['SERVER_PROTOCOL'];
   }

   private function get_referer()
   {
      $this->referer = $this->request['HTTP_REFERER'] ?? SERVER;
   }

   private function get_cookies()
   {
      $this->cookies = $this->objectify($_COOKIE ?? []); // $this->request['HTTP_COOKIE'];
   }

   private function get_session()
   {
      // $this->session = $this->objectify($_SESSION ?? []);
   }

   private function get_html_request_methods()
   {  
      // accomodating methods from html
      if ($this->method == "POST" && $this->body_exists() == true && isset($this->body()->REQUEST_METHOD) && in_array($this->body()->REQUEST_METHOD, ["PUT", "PATCH", "DELETE"]))
      {
         $this->method = $this->body()->REQUEST_METHOD;
      }
   }

   // Called from the Http class //
   // -------------------------- //
   public function set_route_params(array $params)
   {
      $this->params = $this->objectify($params ?? []);
   }

   // Authentication related methods //
   // ------------------------------ //
   private function get_auth_type()
   {

      // Basic Auth
      if ( isset($this->request['PHP_AUTH_USER']) && isset($this->request['PHP_AUTH_PW']) && !empty($this->request['PHP_AUTH_USER']) && !empty($this->request['PHP_AUTH_PW'])) {
         
         $this->auth_type = "Basic";
         // get the auth credentials
         $this->auth_credentials = $this->objectify([
            "username" => $this->request['PHP_AUTH_USER'],
            "password" => $this->request['PHP_AUTH_PW'],
         ]);

      }

      // Digest Auth
      elseif ( isset($this->request['PHP_AUTH_DIGEST']) && !empty($this->request['PHP_AUTH_DIGEST']) ) {
         
         $this->auth_type = "Digest";
         
         // get the auth credentials
         $cred = $this->request['PHP_AUTH_DIGEST'];
         
         // protect against missing data
         $needed_parts = array('nonce'=>1, 'nc'=>1, 'opaque'=>1, 'cnonce'=>1, 'qop'=>1, 'realm'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
         $data = array();
         $keys = implode('|', array_keys($needed_parts));

         preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $cred, $matches, PREG_SET_ORDER);

         foreach ($matches as $m) {
            $data[trim($m[1])] = $m[3] ? $m[3] : $m[4];
            unset($needed_parts[$m[1]]);
         }

         $credentials = $needed_parts ? [] : $data;

         // objectify the credentials
         $this->auth_credentials = $this->objectify($credentials);

      }

      // Session Auth
      elseif ( isset($_SESSION['AUTH']) && !empty($_SESSION['AUTH']) ) {
         
         $this->auth_type = "Session";

         // get the auth credentials
         $this->auth_credentials = $this->objectify($_SESSION['AUTH']);

      }

      // JWT

      // OAuth

      // OAuth2

      // None
      else {
         $this->auth_type = "None";
      }

   }

   public function auth_type()
   {
      return $this->auth_type;
   }

   public function auth_credentials()
   {
      return $this->auth_credentials;
   }

   // method for turning arrays to objects
   private function objectify(array $array)
   {
      if (is_array($array) && $array !== []) {
         $obj = new \stdClass();
         foreach ($array as $key => $value) {
            // convert the value into an object if value is also an array
            if (is_array($value)) {
               $value = $this->objectify($value);
            }
            // assign the value to the key
            $obj->$key = $value;
         }

         $value = null;
         return $obj;
      }

      return null;
   }

   // Publicly accessible methods for getting the request parameters //
   // -------------------------------------------------------------- //

   public function ip()
   { return $this->ip; }

   public function server_ip()
   { return $this->server_ip; }

   public function csrftoken()
   { return $this->csrftoken; }

   public function path()
   { return $this->path; }

   public function scheme()
   { return $this->scheme; }
   
   public function host()
   { return $this->host; }

   public function uri()
   { return $this->uri; }

   public function referer()
   { return $this->referer; }

   public function method()
   { return $this->method; }

   public function files()
   { return $this->files; }

   public function files_exists()
   { return is_object($this->files); }

   public function body()
   { return $this->body; }

   public function body_exists()
   { return is_object($this->body); }

   public function query()
   { return $this->query; }

   public function query_exists()
   { return is_object($this->query); }

   public function params()
   { return $this->params; }

   public function params_exists()
   { return is_object($this->params); }
   
   // --------------------------------------------------------- //

   // private function get_html_request_methods()
   // {  
   //    // accomodating methods from html
   //    if ($this->method == "POST" && $this->body_exists() == true && isset($this->body()->REQUEST_METHOD) && in_array($this->body()->REQUEST_METHOD, ["PUT", "PATCH", "DELETE"]))
   //    {
   //       $this->method = $this->body()->REQUEST_METHOD;
   //    }
   // }

   public function __desctruct()
   {
      // $this->request = null;
   }

}