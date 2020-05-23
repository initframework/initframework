<?php
namespace Framework\Database;
use Framework\Database\Database;
use Framework\Handler\IException;
use PDO;

class Model extends Database
{
   private $conn;
   private $queryConditions;
   private $updateStmt;
   private $table;

   /**
    * The Model construct
    *
    * @param string $table is the table name for the model
    */
   public function __construct(string $table)
   {
      $this->conn = parent::__construct();
      $this->table = $table;
      return $this;
   }

   /**
    * @summary creates a new record in the {$this->table}
    * @param array $inputs is an associative array of field name and the field value
    * @return bool to tell if the execution was successful or not
    */
   public function create(array $inputs) : bool
   {
      try {
         // make the fields bindable
         list($fields, $bindFields, $bindFieldsStr, $bindValues) = $this->createBindables($inputs);
         
         // the command query
         $query = "INSERT INTO {$this->table} ({$fields}) 
         VALUES ({$bindFieldsStr}) ";

         // prepare query
         $stmt = $this->conn->prepare($query);

         // bind fields to their values
         for ($i=0; $i < count($bindFields); $i++) {
            $stmt->bindParam($bindFields[$i], $bindValues[$i]);
         }
   
         // execute the query
         $stmt->execute();

         return true;
      }
      catch(PDOException $ex) {
         throw new IException($ex->getMessage());
      }
      catch(IException $ex) {
         $ex->handle();
      }

   }

   /**
    * Reads records from the table
    *
    * @param string $fields is a string of fields delimited by commas(,)
    * @return array $response is an associative array: flag - a boolean to indicate if data was read; data - an associative array of the selected records OR the error message
    */
   public function read(string $fields) : array
   {
      try {

         $queryConditions = isset($this->queryConditions) ? $this->queryConditions : '';
         $misc = isset($this->misc) ? $this->misc : '';

         $query = "SELECT {$fields} FROM {$this->table} {$queryConditions} {$misc}";

         $stmt = $this->conn->prepare($query);
         
         $stmt->execute();
   
         $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $data = $stmt->fetchAll();

         return $data;
         
      }
      catch(PDOException $ex) {
         throw new IException($ex->getMessage());
      }
      catch(IException $ex) {
         $ex->handle();
      }
   }

   /**
    * Updates a record in the table
    *
    * @param array $updates is an associative array of field name and the field value
    * @return bool to tell if the execution was successful or not
    */
   public function update(array $updates) : bool
   {
      try {

         $sets = $this->updateBindables($updates);

         $queryConditions = isset($this->queryConditions) ? $this->queryConditions : '';

         $query = "UPDATE {$this->table} SET {$sets} {$queryConditions}";
   
         $stmt = $this->conn->prepare($query);
   
         $stmt->execute();

         $this->updateStmt = $stmt;

         $affected = $this->rowsAffected();

         if ($affected > 0){
            return true;
         } else {
            return false;
         }
   
      } catch(PDOException $ex) {
         throw new IException($ex->getMessage());
      }
      catch(IException $ex) {
         $ex->handle();
      }
   }

   /**
    * Deletes a record from the table
    *
    * @return bool to tell if the execution was successful or not
    */
   public function delete() : bool
   {
      try {
         $queryConditions = isset($this->queryConditions) ? $this->queryConditions : '';
         
         $query = "DELETE FROM {$this->table} {$queryConditions}";
   
         $this->conn->exec($query);
         
         $affected = $this->rowsAffected();

         if ($affected > 0){
            return true;
         } else {
            return false;
         }

      } catch(PDOException $ex) {
         throw new IException($ex->getMessage());
      }
      catch(IException $ex) {
         $ex->handle();
      }
   }

   /**
    * Begins transaction
    */
   public function beginTransaction()
   {
      $this->conn->beginTransaction();
   }

   /**
    * Commits Transactions
    */
   public function commit()
   {
      $this->conn->commit();
   }

   /**
    * Rollback Transaction
    */
   public function rollback()
   {
      $this->conn->rollback();
   }

   /**
    * Sets the query conditions
    *
    * @param string $conditions is a string of the query condition E.g "userId = 1"
    * @return Model
    */
   public function where(string $conditions) : Model
   {
      $this->queryConditions = "WHERE " . $conditions;
      return $this;
   }

   /**
    * Sets the MySQL related statements (LIMIT, OFFSET, GROUP BY, ORDER BY, etc...)
    *
    * @param string $stmts is the MySQL related statment E.g "ORDER BY userId ASC"
    * @return Model
    */
   public function misc(string $stmts) : Model
   {
      $this->misc = $stmts;
      return $this;
   }
   
   /**
    * Gets the last inserted id of the connection
    *
    * @return int $lastId is the index of the last record inserted
    */
   public function lastId() : int
   {
      $lastId = $this->conn->lastInsertId();
      return $lastId;
   }

   /**
    * Gets the number of rows affected by an update
    * 
    * @return int $affected is the number of rows affected
    */
   public function rowsAffected() : int
   {
      $affected = $this->updateStmt->rowCount();
      return $affected;
   }

   /**
    * Reads records from multiple tables
    *
    * @param string $joinTables is a string of the tables delimited by commas(,) E.g "table1, table2, table3"
    * @param string $joinFields is a string of the fields for each table delimited by commas(,) E.g "table1.field1, table2.field2, table3.field1". AS can also be used E.g "table1.field1 AS f1, table2.field2 AS f2, table3.field1 AS f3"
    * @return array $response is an associative array: flag - a boolean to indicate if data was read; data - an associative array of the selected records OR the error message
    */
   public function readJoin(string $joinTables, string $joinFields) : array
   {
      try {

         $queryConditions = isset($this->queryConditions) ? $this->queryConditions : '';
         $misc = isset($this->misc) ? $this->misc : '';

         $query = "SELECT {$joinFields} FROM {$joinTables} {$queryConditions} {$misc} ";

         $stmt = $this->conn->prepare($query);
         
         $stmt->execute();
   
         $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $data = $stmt->fetchAll();

         return $data;

      }
      catch(PDOException $ex) {
         throw new IException($ex->getMessage());
      }
      catch(IException $ex) {
         $ex->handle();
      }
   }

   /**
    * A method that checks if a field that satisfy a condition exist
    * 
    * @return bool true is the condition is met
    */
   public function exist() : bool
   {
      try {

         $queryConditions = isset($this->queryConditions) ? $this->queryConditions : '';

         $query = "SELECT COUNT(*) AS count FROM {$this->table} {$queryConditions}";

         $stmt = $this->conn->prepare($query);
         
         $stmt->execute();
   
         $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $response = $stmt->fetchAll();

         if ($response[0]['count'] > 0){
            return true;
         } else {
            return false;
         }
   
      }
      catch(PDOException $ex) {
         throw new IException($ex->getMessage());
      }
      catch(IException $ex) {
         $ex->handle();
      }
   }

   /**
    * A method that carries out SQL functions
    * 
    * @param string $function is the SQL function to be carried out E.g COUNT, AVG, MAX, MIN, SUM
    * @param string $field is the field that is passed to the SQL function
    * @return array $response is an associative array of selected records
    */
   public function queryFunction(string $function, string $field) : array
   {
      try {

         $queryConditions = isset($this->queryConditions) ? $this->queryConditions : '';

         $query = "SELECT {$function}({$field}) FROM {$this->table} {$queryConditions}";

         $stmt = $this->conn->prepare($query);
         
         $stmt->execute();
   
         $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $response = $stmt->fetchAll();

         return $response[0];

      }
      catch(PDOException $ex) {
         throw new IException($ex->getMessage());
      }
      catch(IException $ex) {
         $ex->handle();
      }
   }

   /**
    * A custom method for custom query, a raw query
    * 
    * @param string $query is the raw sql query
    * @param bool $returnResult is a flag to tell if the user is expecting a response
    * @return array $response is an associative array of returned records
    */
   public function custom(string $query, bool $returnResult) : array
   {
      try {

         $stmt = $this->conn->prepare($query);
         
         $stmt->execute();

         // if returned result is expected 
         if ($returnResult == true) {
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

            $response = $stmt->fetchAll();

            return $response;
         }

      }
      catch(PDOException $ex) {
         throw new IException($ex->getMessage());
      }
      catch(IException $ex) {
         $ex->handle();
      }
   }

   /**
    * This forms a string with the field names and packs all the field values into an array
    * 
    * @param array $inputs is an associative array of field name and the field value
    * @return array $query is an array of string fields, string bindFields, array bindValues 
    */
   private function createBindables(array $inputs) : array
   {
      $fields = array();
      $bindFields = array();
      $bindValues = array();

      foreach ($inputs as $field => $value) {
         $fields[] = $field;
         $bindFields[] = ":" . $field;
         $bindValues[] = $this->sanitize($value ?? "NULL");
      }

      $fields = implode(',', $fields);
      $bindFieldsStr = implode(',', $bindFields);
      
      $query = [$fields, $bindFields, $bindFieldsStr, $bindValues];
      return $query;
   }

   /**
    * This method turns the array into a MySQL SET statement
    *
    * @param array $updates is an associative array of field name and the field value
    * @return string $sets a string of MySQL SET statement
    */
   private function updateBindables(array $updates) : string
   {
      $sets = array();

      foreach ($updates as $field => $value) {
         $value = $this->sanitize($value ?? "NULL");
         $sets[] = $field . " = '" . $value . "'";
      }

      $sets = implode(', ', $sets);

      return $sets;
   }

   /**
    * This sanitizes a value before it is entered into the database
    * 
    * @param string $value is the value to be sanitized
    * @return string $value is a sanitized value
    */
   private function sanitize(string $value) : string
   {
      $value ?? "";

      // $value = real_escape_string($value);
      // $value = (null !==  (get_magic_quotes_gpc())) ? stripcslashes($value) : $value;
      $value = strip_tags($value);
      $value = htmlentities($value);
      // return $value;
      // echo $value . "<br>";
      return $value;
   }

   // public function __destruct()
   // {
   //    $this->conn = null;
   //    $this->table = null;
   //    $this->queryConditions = null;
   //    $this->updateStmt = null;
   // }

}

?>