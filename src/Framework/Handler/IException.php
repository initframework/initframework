<?php
/**
 * @package		InitFramework
 * @author		Ebuka Odini
 * @copyright	Copyright (c) 2018 - 2020, ebukaodini&co, (https://www.github.com/ebukaodini/)
 * @license		https://opensource.org/licenses/MIT
 * @link		   https://www.github.com/ebukaodini/
*/

namespace Framework\Handler;
use Exception;

/**
 * IException class
*/
class IException extends Exception
{
   public function handle(string $channel = 'web', bool $backtrace = false)
   {
      $file = $this->getFile();
      $line = $this->getLine();
      $message = $this->getMessage();
      if ($backtrace == true) {
         $backtraces = \debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT,10);
         $backtraces = array_reverse($backtraces);
      } else {
         $backtraces = [];
      }

      if (ERROR_DISPLAY == true) {
         switch ($channel) {
            case 'web':
               $this->displayWeb($file, $line, $message, $backtraces);
               break;
            case 'api':
               $this->displayApi($file, $line, $message, $backtraces);
               break;
            case 'cli':
               $this->displayCli($file, $line, $message, $backtraces);
               break;
            default:
               $this->displayWeb($file, $line, $message, $backtraces);
               break;
         }
      }

      $this->log($file, $line, $message);

      $this->email($file, $line, $message);

      exit;
   }

   private function displayWeb($file, $line, $message, $backtraces)
   {
      $display = "";
      $display .= "$message in $file on line $line" . "\n";
      foreach ($backtraces as $backtrace) {
         $display .= json_encode($backtrace) . "\n";
      }
      echo $display;
   }

   private function displayApi($file, $line, $message, $backtraces)
   {
      $display = "";
      $display .= "$message in $file on line $line" . "\n";
      foreach ($backtraces as $backtrace) {
         $display .= json_encode($backtrace) . "\n";
      }
      echo json_encode($display);
   }

   private function displayCli($file, $line, $message, $backtraces)
   {
      $display = "";
      $display .= "$message in $file on line $line" . "\n";
      foreach ($backtraces as $backtrace) {
         $display .= json_encode($backtrace) . "\n";
      }
      echo $display;
   }

   private function log($file, $line, $message)
   {
      if (ERROR_LOG == true) {
         $log = "[" . date("D d-m-Y h:i:s A T") . "] - $message in $file on line $line";
         @error_log($log . "\n", 3, ERROR_LOG_FILE);
      }
   }

   private function email($file, $line, $message)
   {
      if (EMAIL_LOG == true) {
         $log = "[" . date("D d-m-Y h:i:s A T") . "] - $message in $file on line $line";
         @error_log($log . "\n", 1, EMAIL_LOG_ADDRESS);
      }
   }
}