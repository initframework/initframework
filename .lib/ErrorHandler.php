<?php

// register_shutdown_function(function() {
//    // $backtraces = error_get_last();
//    // echo json_encode($backtraces, JSON_PRETTY_PRINT);
//    echo "<br><small>A log of this error can be found at " . ERROR_LOG_FILE . "</small><br><img align='top' width='25em' src='" . ASSETS_PATH . "imgs/favicon.ico'> InitFramework";
//    exit;
// });

set_error_handler(function() {
   $backtraces = \debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT,10);
   for ($i = 0; $i < count($backtraces); $i++) {
      $backtrace = $backtraces[$i];
      if ($i > 0) {
         // comment this after development
         if ($i == 1) continue;
         $file = (string)$backtrace['file'] ?? "unknown file";
         $line = (string)$backtrace['line'] ?? "unknown line";
         echo "<span style='margin-left: 1em;'>&raquo; $file on line $line</span><br>";
         @error_log("\t>> $file on line $line\n", 3, ERROR_LOG_FILE);
      } else {
         $code = (string)$backtrace['args'][0];
         $message = (string)$backtrace['args'][1];
         echo sprintf("<strong>[%s] %s</strong><br><br>", $code, $errmsg ?? $message);
         @error_log("[" . date("D d-m-Y h:i:s A T") . "] [$code] $message\n", 3, ERROR_LOG_FILE);
         if (EMAIL_LOG == true) {
            $file = (string)$backtrace['args'][2] ?? "unknown file";
            $line = (string)$backtrace['args'][3] ?? "unknown line";
            @error_log("[" . date("D d-m-Y h:i:s A T") . "] [$code] $message in $file on line $line", 1, EMAIL_LOG_ADDRESS);
         }
      }
   }

   echo "<br><small>A log of this error can be found at " . ERROR_LOG_FILE . "</small><br><img align='top' width='25em' src='" . ASSETS_PATH . "imgs/favicon.ico'> InitFramework";
   exit;
}, E_ALL);
