<?php

set_error_handler("errorHandler", E_STRICT);
set_error_handler("errorHandler", E_ALL);

function errorHandler() {
   $backtraces = \debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT,10);
   if (file_exists(LOG_FILE) == false) touch(LOG_FILE);
   $haltFlow = false;
   
   for ($i = 0; $i < count($backtraces); $i++) {
      $backtrace = $backtraces[$i];
      if ($i > 0) {
         if (!empty($backtrace['file']) || !empty($backtrace['file'])) {
            $file = (string)$backtrace['file'] ?? "unknown file";
            $line = (string)$backtrace['line'] ?? "unknown line";
            
            if (DISPLAY_ERROR == true) {
               if (PHP_SAPI == 'cli') {
                  echo "   >> $file on line $line\n";
               } else {
                  echo "<span style='margin-left: 1em;'>&raquo; $file on line $line</span><br>";
               }
            }
            
            if (LOG_ERROR == true) {
               @error_log("\t>> $file on line $line\n", 3, LOG_FILE);
            }
         }
      } else {
         $code = (string)$backtrace['args'][0] ?? "0";
         $message = (string)$backtrace['args'][1] ?? "Unknown Error";

         if (LOG_ERROR == true) {
            @error_log("[" . date("D d-m-Y h:i:s A T") . "] [$code] $message\n", 3, LOG_FILE);
         }
         
         if (SEND_EMAIL_LOG == true) {
            $file = (string)$backtrace['args'][2] ?? "unknown file";
            $line = (string)$backtrace['args'][3] ?? "unknown line";
            @error_log("[" . date("D d-m-Y h:i:s A T") . "] [$code] $message in $file on line $line", 1, SEND_EMAIL_LOG_ADDRESS);
         }

         if (DISPLAY_ERROR == true) {
            if (PHP_SAPI == 'cli') {
               echo "[$code] $message\n\n";
            } else {
               echo sprintf("<strong>[%s] %s</strong><br><br>", $code, $message);
            }
         }
         else {
            if (substr_count($backtraces[$i+1]['file'], ".lib\\", 0) > 0)
            $haltFlow = true;
         }

      }
   }

   if (DISPLAY_ERROR == true) {
      echo (PHP_SAPI == 'cli') ? "\nA log of this error can be found at " . LOG_FILE . "\n" : "<br><small>A log of this error can be found at " . str_replace('/', '\\', LOG_FILE) . "</small><br><img align='top' width='25em' src='" . SERVER ."/". ASSETS_PATH . "imgs/favicon.ico'> InitFramework";
      exit;
   } else {
      if ($haltFlow) serverError($_ENV['REQUEST']);  
   }
}