<?php
namespace Library\Database;
use PDO;
use PDOException;
use Exception;

class Database
{

   private $driver = DB_DRIVER;
   private $host = DB_HOST;
   private $port = DB_PORT;
   private $database = DB_DATABASE;
   private $username = DB_USERNAME;
   private $password = DB_PASSWORD;

   protected function __construct(){
      // $conn;

      try {
         $conn = new PDO("$this->driver:host=$this->host;dbname=$this->database;port=$this->port", $this->username, $this->password);
         // set the PDO error mode to exception
         $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         if (!$conn) {
            throw new PDOException("Database connection failed.");
         }
      }
      catch(PDOException $ex) {
         exit($ex->getMessage());
      }

      return $conn;
   }

}

?>