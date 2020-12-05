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

   public static function getInstance(){
      if (is_null(self::$instance)) {
         try {
            self::$instance = new PDO(self::$driver.":host=".self::$host.";dbname=".self::$database.";port=".self::$port, self::$username, self::$password);
            // set the PDO error mode to exception
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if (!self::$instance) {
               throw new PDOException("Database connection failed.");
            }
         }
         catch(PDOException $ex) {
            trigger_error($ex->getMessage());
         }
      }
      
      return self::$instance;
   }

}
