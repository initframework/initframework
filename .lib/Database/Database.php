<?php
namespace Library\Database;
use PDO;
use PDOException;
use Exception;

class Database
{

   private static $driver = DB_DRIVER;
   private static $host = DB_HOST;
   private static $port = DB_PORT;
   private static $database = DB_DATABASE;
   private static $username = DB_USERNAME;
   private static $password = DB_PASSWORD;
   private static $instance = null;

   // private $driver = DB_DRIVER;
   // private $host = DB_HOST;
   // private $port = DB_PORT;
   // private $database = DB_DATABASE;
   // private $username = DB_USERNAME;
   // private $password = DB_PASSWORD;
   // private $instance = null;

   // protected function __construct(){
   //    if (is_null($this->instance)) {
   //       try {
   //          $conn = new PDO("$this->driver:host=$this->host;dbname=$this->database;port=$this->port", $this->username, $this->password);
   //          // set the PDO error mode to exception
   //          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   //          if (!$conn) {
   //             throw new PDOException("Database connection failed.");
   //          }
   //       }
   //       catch(PDOException $ex) {
   //          exit($ex->getMessage());
   //       }
   //       $this->instance = $conn;
   //    }
      
   //    return $this->instance;
   // }

   public static function getInstance(){
      if (is_null(self::$instance)) {
         try {
            $conn = new PDO(self::$driver.":host=".self::$host.";dbname=".self::$database.";port=".self::$port, self::$username, self::$password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if (!$conn) {
               throw new PDOException("Database connection failed.");
            }
         }
         catch(PDOException $ex) {
            trigger_error($ex->getMessage());
         }
         self::$instance = $conn;
      }
      
      return self::$instance;
   }


}
