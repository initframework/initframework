<?php
namespace Framework\Database;
use PDO;
use PDOException;
use Framework\FrameworkException;

class Database
{

   private $driver = DB_DRIVER;
   private $host = DB_HOST;
   private $port = DB_PORT;
   private $database = DB_DATABASE;
   private $username = DB_USERNAME;
   private $password = DB_PASSWORD;

   protected function __construct(){
      $conn;

      try {
         $conn = new PDO("$this->driver:host=$this->host;dbname=$this->database;port=$this->port", $this->username, $this->password);
         // set the PDO error mode to exception
         $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      catch(PDOException $e)
      {
         // handle error
         Error::internalError($e->getMessage());
      }

      return $conn;
   }

}

?>