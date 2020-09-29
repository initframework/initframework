<?php
namespace Library\Database;
use PDOException;
use PDO;
use Exception;

class Model extends Database
{
   private $conn;
   private $affected;
   private $table;

   private $joinFields;
   private $joinCondition;
   private $joins = [];

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
         list($fields, $bindFields, $bindFieldsStr, $bindValues) = $this->bindCreate($inputs);
         
         // the command query
         $query = "INSERT INTO {$this->table} ({$fields}) 
         VALUES ({$bindFieldsStr}) ";

         // prepare query
         $stmt = $this->conn->prepare($query);

         // bind fields to their values
         for ($i = 0; $i < count($bindFields); $i++) {
            $stmt->bindParam($bindFields[$i], $bindValues[$i]);
         }

         // execute the query
         $stmt->execute();

         $this->affected = $stmt->rowCount();

         if ($stmt->rowCount() > 0){
            return true;
         } else {
            return false;
         }
      }
      catch(PDOException $ex) {
         exit($ex->getMessage());
      }

   }

   /**
    * This forms a string with the field names and packs all the field values into an array
    * 
    * @param array $inputs is an associative array of field name and the field value
    * @return array $query is an array of string fields, string bindFields, array bindValues 
    */
   private function bindCreate(array $inputs) : array
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
    * Updates a record in the table
    *
    * @param array $updates is an associative array of field name and the field value
    * @return bool to tell if the execution was successful or not
    */
   public function update(array $updates, string $condition = "WHERE 1") : bool
   {
      try {

         $sets = $this->bindUpdate($updates);

         $query = "UPDATE {$this->table} SET {$sets} {$condition}";
         
         $stmt = $this->conn->prepare($query);
   
         $stmt->execute();

         $this->affected = $stmt->rowCount();

         if ($stmt->rowCount() > 0){
            return true;
         } else {
            return false;
         }
   
      } catch(PDOException $ex) {
         exit($ex->getMessage());
      }
   }
 
   /**
    * This method turns the array into a MySQL SET statement
    *
    * @param array $updates is an associative array of field name and the field value
    * @return string $sets a string of MySQL SET statement
    */
   private function bindUpdate(array $updates) : string
   {
      $sets = array();

      foreach ($updates as $field => $value) {
         $value = $this->sanitize($value ?? "NULL");
         $sets[] = $field . " = '" . $value . "'";
      }

      return implode(', ', $sets);
   }

   /**
    * Deletes a record from the table
    *
    * @return bool to tell if the execution was successful or not
    */
   public function delete(string $condition = "WHERE 1") : bool
   {
      try {
         
         $query = "DELETE FROM {$this->table} {$condition}";
   
         $stmt = $this->conn->prepare($query);
   
         $stmt->execute();
         
         $this->affected = $stmt->rowCount();

         if ($stmt->rowCount() > 0){
            return true;
         } else {
            return false;
         }

      } catch(PDOException $ex) {
         exit($ex->getMessage());
      }
   }

   /**
    * Gets the number of rows affected by last query
    * 
    * @return int $affected is the number of rows affected
    */
   public function rowsAffected() : int
   {
      return $this->affected;
   }

   /**
    * A method that checks if a field that satisfy a condition exist
    * 
    * @return bool true is the condition is met
    */
   public function exist(string $condition = "WHERE 1") : bool
   {
      try {

         $query = "SELECT COUNT(*) AS count FROM {$this->table} {$condition}";

         $stmt = $this->conn->prepare($query);
         
         $stmt->execute();
   
         $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $response = $stmt->fetchAll();

         if ($response[0]['count'] > 0){
            return true;
         } else {
            return false;
         }
   
      }
      catch(PDOException $ex) {
         exit($ex->getMessage());
      }
   }

   /**
    * Reads records from the table
    *
    * @param string $fields is a string of fields delimited by commas(,)
    * @return array $response is an associative array: flag - a boolean to indicate if data was read; data - an associative array of the selected records OR the error message
    */
   public function findAll(string $fields = "*", string $condition = "WHERE 1") : array
   {
      try {

         $query = "SELECT {$fields} FROM {$this->table} {$condition}";

         $stmt = $this->conn->prepare($query);
         
         $stmt->execute();
   
         $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $data = $stmt->fetchAll();

         return $data;
         
      }
      catch(PDOException $ex) {
         exit($ex->getMessage());
      }
   }

   /**
    * Reads records from the table
    *
    * @param string $fields is a string of fields delimited by commas(,)
    * @return array $response is an associative array: flag - a boolean to indicate if data was read; data - an associative array of the selected records OR the error message
    */
   public function findOne(string $fields = "*", string $condition = "WHERE 1") : array
   {
      try {

         $query = "SELECT {$fields} FROM {$this->table} {$condition} LIMIT 1";

         $stmt = $this->conn->prepare($query);
         
         $stmt->execute();
   
         $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $data = $stmt->fetchAll();

         return $data[0];
         
      }
      catch(PDOException $ex) {
         exit($ex->getMessage());
      }
   }

   /**
    * Reads records from the table
    *
    * @param string $fields is a string of fields delimited by commas(,)
    * @return Model $response is an associative array: flag - a boolean to indicate if data was read; data - an associative array of the selected records OR the error message
    */
   public function find(array $fields = ["*"], string $condition = "WHERE 1") : Model
   {
      try {

         $this->joins = [];
         $arrFields = [];
         foreach ($fields as $field) {
            $arrFields[] = "{$this->table}.{$field}";
         }
         $this->joinFields = implode(", ", $arrFields);
         $this->joinCondition = $condition;

         return $this;

      }
      catch(PDOException $ex) {
         exit($ex->getMessage());
      }
   }

   public function innerJoin(string $table, string $on) : Model
   {
      try {

         $this->joins = "INNER JOIN {$table} ON {$on}";

         return $this;
         
      }
      catch(PDOException $ex) {
         exit($ex->getMessage());
      }
   }

   public function leftJoin(string $table, string $on) : Model
   {
      try {

         $this->joins = "LEFT JOIN {$table} ON {$on}";

         return $this;
         
      }
      catch(PDOException $ex) {
         exit($ex->getMessage());
      }
   }

   public function rightJoin(string $table, string $on) : Model
   {
      try {

         $this->joins = "RIGHT JOIN {$table} ON {$on}";

         return $this;
         
      }
      catch(PDOException $ex) {
         exit($ex->getMessage());
      }
   }

   public function fullJoin(string $table, string $on) : Model
   {
      try {

         $this->joins = "FULL OUTER JOIN {$table} ON {$on}";

         return $this;
         
      }
      catch(PDOException $ex) {
         exit($ex->getMessage());
      }
   }

   public function join() : array
   {
      try {

         $joins = implode(" ", $this->joins);

         $query = "SELECT {$this->joinFields} FROM {$this->table} {$joins} {$this->joinCondition}";

         $stmt = $this->conn->prepare($query);
         
         $stmt->execute();
   
         $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $data = $stmt->fetchAll();

         return $data;
         
      }
      catch(PDOException $ex) {
         exit($ex->getMessage());
      }
   }

   /**
    * A custom method for custom query, a raw query
    * 
    * @param string $query is the raw sql query
    * @param bool $returnResult is a flag to tell if the user is expecting a response
    * @return array $response is an associative array of returned records
    */
   public function query(string $query, bool $results = false) : array
   {
      try {

         $stmt = $this->conn->prepare($query);
         
         $stmt->execute();

         // if returned result is expected
         if ($results == true) {
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            $response = $stmt->fetchAll();

            return $response;

         } else {
            
            $this->affected = $stmt->rowCount();

            if ($stmt->rowCount() > 0) {
               return true;
            } else {
               return false;
            }

         }

      }
      catch(PDOException $ex) {
         exit($ex->getMessage());
      }
   }

   /**
    * Begins transaction
    */
   public function transaction()
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
    * This sanitizes a value before it is entered into the database
    * 
    * @param string $value is the value to be sanitized
    * @return string $value is a sanitized value
    */
   private function sanitize(string $value) : string
   {
      return htmlentities($value ?? "", ENT_QUOTES);
   }

   public function __destruct()
   {
      $this->conn = null;
      $this->table = null;
      $this->affected = null;
   }

}

?>