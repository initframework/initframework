<?php
namespace Library\Database;
use Library\Database\Database;
use PDOException;
use PDO;
use Exception;

class Model extends Database
{
   private static $conn;
   private static $affected;
   private static $table;

   private static $joinFields;
   private static $joinCondition;
   private static $joins = [];

   /**
    * The Model construct
    *
    * @param string $table is the table name for the model
    */
   private static function instance()
   {
      self::$conn = self::getInstance();
      self::$table = DB_PREFIX . get_called_class()::$table;
   }

   /**
    * @summary creates a new record in the ". self::$table ."
    * @param array $inputs is an associative array of field name and the field value
    * @return bool to tell if the execution was successful or not
    */
   public static function create(array $inputs) : bool
   {
      self::instance();
      try {
         // make the fields bindable
         list($fields, $bindFields, $bindFieldsStr, $bindValues) = self::bindCreate($inputs);
         $fields = implode(", ", $fields);

         // the command query
         $query = "INSERT INTO ". self::$table ." ({$fields}) 
         VALUES ({$bindFieldsStr}) ";

         // prepare query
         $stmt = self::$conn->prepare($query);

         // bind fields to their values
         for ($i = 0; $i < count($bindFields); $i++) {
            $stmt->bindParam($bindFields[$i], $bindValues[$i]);
         }

         // execute the query
         $stmt->execute();

         self::$affected = $stmt->rowCount();

         if ($stmt->rowCount() > 0){
            return true;
         } else {
            return false;
         }
      }
      catch(PDOException $ex) {
         trigger_error($ex->getMessage());
      }

   }

   /**
    * @summary creates a multiple records in the ". self::$table ."
    * @param array $inputs is a multi dimensional associative array of field name and the field value
    * @return bool to tell if the execution was successful or not
    */
   public static function createMany(array ...$inputs) : bool
   {
      self::instance();

      try {
         // make the fields bindable
         foreach ($inputs as $input) {
            list($fields, $bindFields[], $bindFieldsStr[], $bindValues[]) = self::bindCreate($input);
         }
         
         $fields = implode(", ", $fields);
         $_ENV['count'] = 0;
         $bindFieldsStr = implode(", ", array_map(function($fields) { $fields = implode(",",array_map(function($field) { return $field . "_" . $_ENV['count']++; }, explode(",", $fields))); return "($fields)"; }, $bindFieldsStr));
         $_ENV['count'] = 0;
         $bindFields = array_map(function($fields) { return $fields . "_" . $_ENV['count']++; }, array_merge(...$bindFields));
         $_ENV['count'] = 0;
         $bindValues = array_merge(...$bindValues);

         // the command query
         $query = "INSERT INTO ". self::$table ." ({$fields}) 
         VALUES {$bindFieldsStr} ";

         // prepare query
         $stmt = self::$conn->prepare($query);

         // bind fields to their values
         for ($i = 0; $i < count($bindFields); $i++) {
            $stmt->bindParam($bindFields[$i], $bindValues[$i]);
         }

         // execute the query
         $stmt->execute();

         self::$affected = $stmt->rowCount();

         if ($stmt->rowCount() > 0){
            return true;
         } else {
            return false;
         }
      }
      catch(PDOException $ex) {
         trigger_error($ex->getMessage());
      }

   }

   /**
    * This forms a string with the field names and packs all the field values into an array
    * 
    * @param array $inputs is an associative array of field name and the field value
    * @return array $query is an array of string fields, string bindFields, array bindValues 
    */
   private static function bindCreate(array $inputs) : array
   {
      self::instance();
      $fields = array_keys($inputs);
      $bindFields = array_map(function($field) { return ":" . $field; }, $fields);
      $bindValues = array_map(function($value) { return self::sanitize($value); }, array_values($inputs));
      $bindFieldsStr = implode(',', $bindFields);
      
      return [$fields, $bindFields, $bindFieldsStr, $bindValues];
   }

   /**
    * Gets the last inserted id of the connection
    *
    * @return int $lastId is the index of the last record inserted
    */
   public static function lastId() : int
   {
      self::instance();
      $lastId = self::$conn->lastInsertId();
      return $lastId;
   }

   /**
    * Updates a record in the table
    *
    * @param array $updates is an associative array of field name and the field value
    * @return bool to tell if the execution was successful or not
    */
   public static function update(array $updates, string $condition = "WHERE 1") : bool
   {
      self::instance();
      try {

         $sets = self::bindUpdate($updates);

         $query = "UPDATE ". self::$table ." SET {$sets} {$condition}";
         
         $stmt = self::$conn->prepare($query);
   
         $stmt->execute();

         self::$affected = $stmt->rowCount();

         if ($stmt->rowCount() > 0){
            return true;
         } else {
            return false;
         }
   
      } catch(PDOException $ex) {
         trigger_error($ex->getMessage());
      }
   }
 
   /**
    * This method turns the array into a MySQL SET statement
    *
    * @param array $updates is an associative array of field name and the field value
    * @return string $sets a string of MySQL SET statement
    */
   private static function bindUpdate(array $updates) : string
   {
      self::instance();
      $sets = array();

      foreach ($updates as $field => $value) {
         $value = self::sanitize($value ?? "NULL");
         $sets[] = $field . " = '" . $value . "'";
      }

      return implode(', ', $sets);
   }

   /**
    * Deletes a record from the table
    *
    * @return bool to tell if the execution was successful or not
    */
   public static function delete(string $condition = "WHERE 1") : bool
   {
      self::instance();
      try {
         
         $query = "DELETE FROM ". self::$table ." {$condition}";
   
         $stmt = self::$conn->prepare($query);
   
         $stmt->execute();
         
         self::$affected = $stmt->rowCount();

         if ($stmt->rowCount() > 0){
            return true;
         } else {
            return false;
         }

      } catch(PDOException $ex) {
         trigger_error($ex->getMessage());
      }
   }

   /**
    * Gets the number of rows affected by last query
    * 
    * @return int $affected is the number of rows affected
    */
   public static function rowsAffected() : int
   {
      self::instance();
      return self::$affected;
   }

   /**
    * A method that checks if a field that satisfy a condition exist
    * 
    * @return bool true is the condition is met
    */
   public static function exist(string $condition = "WHERE 1") : bool
   {
      self::instance();
      try {

         $query = "SELECT COUNT(*) AS count FROM ". self::$table ." {$condition}";

         $stmt = self::$conn->prepare($query);
         
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
         trigger_error($ex->getMessage());
      }
   }

   /**
    * Reads records from the table
    *
    * @param string $fields is a string of fields delimited by commas(,)
    * @return array $response is an associative array: flag - a boolean to indicate if data was read; data - an associative array of the selected records OR the error message
    */
   public static function findAll(string $fields = "*", string $condition = "WHERE 1") : array
   {
      self::instance();
      try {

         $query = "SELECT {$fields} FROM ". self::$table ." {$condition}";
         // exit($query);

         $stmt = self::$conn->prepare($query);
         
         $stmt->execute();
   
         $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $data = $stmt->fetchAll();

         return $data;
         
      }
      catch(PDOException $ex) {
         trigger_error($ex->getMessage());
      }
   }

   /**
    * Reads records from the table
    *
    * @param string $fields is a string of fields delimited by commas(,)
    * @return array $response is an associative array: flag - a boolean to indicate if data was read; data - an associative array of the selected records OR the error message
    */
   public static function findOne(string $fields = "*", string $condition = "WHERE 1") : array
   {
      self::instance();
      try {

         $query = "SELECT {$fields} FROM ". self::$table ." {$condition} LIMIT 1";

         $stmt = self::$conn->prepare($query);
         
         $stmt->execute();
   
         $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $data = $stmt->fetchAll();

         return $data[0];
         
      }
      catch(PDOException $ex) {
         trigger_error($ex->getMessage());
      }
   }

   /**
    * Reads records from the table
    *
    * @param string $fields is a string of fields delimited by commas(,)
    * @return Model $response is an associative array: flag - a boolean to indicate if data was read; data - an associative array of the selected records OR the error message
    */
   public static function findJoin(string $fields = "*", string $condition = "WHERE 1") : Model
   {
      self::instance();
      try {

         self::$joins = [];
         // foreach ($fields as $field) {
         //    $arrFields[] = DB_PREFIX . "". self::$table .".{$field}";
         // }
         self::$joinFields = $fields;
         self::$joinCondition = $condition;

         return new self();

      }
      catch(PDOException $ex) {
         trigger_error($ex->getMessage());
      }
   }

   public static function innerJoin(string $table, string $on) : Model
   {
      self::instance();
      try {

         self::$joins[] = "INNER JOIN {$table} ON {$on}";

         return new self();
         
      }
      catch(PDOException $ex) {
         trigger_error($ex->getMessage());
      }
   }

   public static function leftJoin(string $table, string $on) : Model
   {
      self::instance();
      try {

         self::$joins[] = "LEFT JOIN {$table} ON {$on}";

         return new self();
         
      }
      catch(PDOException $ex) {
         trigger_error($ex->getMessage());
      }
   }

   public static function rightJoin(string $table, string $on) : Model
   {
      self::instance();
      try {

         self::$joins[] = "RIGHT JOIN {$table} ON {$on}";

         return new self();
         
      }
      catch(PDOException $ex) {
         trigger_error($ex->getMessage());
      }
   }

   public static function fullJoin(string $table, string $on) : Model
   {
      self::instance();
      try {

         self::$joins[] = "FULL OUTER JOIN {$table} ON {$on}";

         return new self();
         
      }
      catch(PDOException $ex) {
         trigger_error($ex->getMessage());
      }
   }

   public static function join() : array
   {
      self::instance();
      try {

         $joins = implode(" ", self::$joins);

         $query = "SELECT ". self::$joinFields ." FROM ". self::$table ." {$joins} ". self::$joinCondition ."";

         $stmt = self::$conn->prepare($query);
         
         $stmt->execute();
   
         $stmt->setFetchMode(PDO::FETCH_ASSOC);

         $data = $stmt->fetchAll();

         return $data;
         
      }
      catch(PDOException $ex) {
         trigger_error($ex->getMessage());
      }
   }

   /**
    * A custom method for custom query, a raw query
    * 
    * @param string $query is the raw sql query
    * @param bool $returnResult is a flag to tell if the user is expecting a response array or a boolean
    * @return array|bool $response is either an associative array of returned records or a boolean value
    */
   public static function query(string $query, bool $results = false)
   {
      self::instance();
      try {

         $stmt = self::$conn->prepare($query);
         $result = $stmt->execute();

         if (!$result) {
            throw new PDOException();
         }
         
         // if returned result is expected
         if ($results == true) {
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            $response = $stmt->fetchAll();

            return $response;

         } else {
            
            self::$affected = $stmt->rowCount();

            if ($stmt->rowCount() > 0) {
               return true;
            } else {
               return false;
            }

         }

      }
      catch(\Throwable $ex) {
         trigger_error($ex->getMessage());
      }
   }

   /**
    * Begins transaction
    */
   public static function transaction()
   {
      self::instance();
      self::$conn->beginTransaction();
   }

   /**
    * Commits Transactions
    */
   public static function commit()
   {
      self::instance();
      self::$conn->commit();
   }

   /**
    * Rollback Transaction
    */
   public static function rollback()
   {
      self::instance();
      self::$conn->rollback();
   }

   /**
    * This sanitizes a value before it is entered into the database
    * 
    * @param string $value is the value to be sanitized
    * @return string $value is a sanitized value
    */
   private static function sanitize(string $value) : string
   {
      self::instance();
      return $value;
      // return htmlentities($value ?? "", ENT_QUOTES);
   }

   public function __destruct()
   {
      self::instance();
      self::$conn = null;
      self::$table = null;
      self::$affected = null;
   }

}

// function join

// INNER
// SELECT column_name(s)
// FROM table1
// INNER JOIN table2
// ON table1.column_name = table2.column_name;

// LEFT
// SELECT column_name(s)
// FROM table1
// LEFT JOIN table2
// ON table1.column_name = table2.column_name;

// RIGHT
// SELECT column_name(s)
// FROM table1
// RIGHT JOIN table2
// ON table1.column_name = table2.column_name;

// FULL
// SELECT column_name(s)
// FROM table1
// FULL OUTER JOIN table2
// ON table1.column_name = table2.column_name
// WHERE condition;

// SELF
// SELECT column_name(s)
// FROM table1 T1, table1 T2
// WHERE condition;

// UNION
// SELECT column_name(s) FROM table1
// UNION
// SELECT column_name(s) FROM table2;