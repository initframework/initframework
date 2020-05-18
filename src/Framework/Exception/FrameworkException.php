<?php
namespace Framework;
use Framework\Response;
use Monolog\Monolog;
use Exception;

class FrameworkException extends Exception
{

   private $response;

   public function __construct(Response $res)
   {
      $this->response = $res;
   }

   // Logging
   // 0 => send log to php system logger
   // 1 => send log to developers email
   // 3 => send log to file
   // 4 => send log to SAPI handler

   public function log_exception(string $errMsg = "")
   {
      // \error_log($errMsg, 3, 'logger.log');
      // die($errMsg);
      // $this->response->send($errMsg, 500);
      // \http_response_code(500);
      // \debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
   }

   public function email_developer(string $errMsg = "")
   {

   }

   public function show_developer(string $errMsg = "")
   {
      // only show the developer if debugging is on
      if (ERROR_DISPLAY == true) {
         $backtraces = \debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT,10);//DEBUG_BACKTRACE_PROVIDE_OBJECT
         $backtraces = array_reverse($backtraces);
         $level = 1;
         foreach ($backtraces as $backtrace) {
            echo \str_repeat("&nbsp;&nbsp;", $level) . json_encode($backtrace) . "<br><br><br>";
            $level = $level + 4;
         }
      }
   }

   public function __destruct()
   {
      \http_response_code(500);
   }

}