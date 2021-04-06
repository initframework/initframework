<?php

namespace Library\Database;

use Library\Database\Model;
use Closure;
use PDOException;

// class Schema extends Database
// class Schema extends Model
class Schema
{

   private $change = "";
   private $open = false;
   private $fields = [];
   private $nulls = [];
   private $field_index = -1;
   private $field;
   private $keys = [];
   private $key_index = 0;
   private $conn = null;
   private static $schema;
   public static $resultArray;
   public static $result;

   // Schema methods

   public static function create(string $table, Closure $schema, bool $strict = true, string $model = null)
   {
      try {
         if (!empty($table)) {
            $schema(new Schema());
            $body = self::$schema;

            // Create query for table
            $query = [];
            $count = 0;
            foreach ($body->fields as $field) {
               $null = (isset($body->nulls[$count]) && $body->nulls[$count] == true) ? "" : " NOT NULL";
               $subquery = implode(" ", $field) . $null;

               // the after attribute in a query is supposed to appear towards the end of a query, at least after the not null attribute
               // if both attribute exists in the query
               if (strpos($subquery, ' AFTER') != false && strpos($subquery, ' NOT NULL') != false) {
                  // and the position of the not null appears after the after attribute
                  if (strpos($subquery, ' NOT NULL') > strpos($subquery, ' AFTER')) {
                     // move the not null attribute to the current position of the after
                     // remove the current not null attribute and add it in the pos of the after attribute
                     $subquery = str_replace(' NOT NULL', '', $subquery);
                     $subquery = substr_replace($subquery, ' NOT NULL ', strpos($subquery, ' AFTER'), 1);
                  }
               }

               $query[] = $subquery;
               $count++;
            }
            foreach ($body->keys as $key) {
               $query[] = $key;
            }

            $sqlMode = $strict == false ? "SET SQL_MODE = ' '; " : "SET SQL_MODE = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION'; ";
            $sql = $sqlMode . "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . $table . " ( " . implode(", ", $query) . " ) ENGINE=" . DB_ENGINE . " DEFAULT CHARSET=" . DB_DEFAULT_CHARSET . " COLLATE=" . DB_COLLATION;

            if ($_ENV['show_query'] == true && strpos($sql, 'schema_migration') == false) echo "\n" . $sql . "\n";

            // if (strpos($sql, 'schema_migration') != false) {
            $result = Model::query($sql);
            self::$resultArray[] = $result;
            echo $result == true ? "\n\tCreate $table: successful" : "\n\tCreate $table: failed ";
            self::$result = in_array(false, self::$resultArray) ? false : true;
            // }

            if (!is_null($model) && !empty($model)) {
               echo "\n";
               // create a model for this migration
               (new \Library\Console\Init())->_init_new_model($model, $table);
            }

            // clear properties
            self::clear($body);
         }
      } catch (\Throwable $ex) {
         trigger_error($ex->getMessage());
      }
   }

   public static function alter(string $table, Closure $schema, bool $strict = true)
   {
      try {
         if (!empty($table)) {
            $schema(new Schema());
            $body = self::$schema;

            // Create query for table
            $query = [];
            $count = 0;
            foreach ($body->fields as $field) {
               $null = (isset($body->nulls[$count]) && $body->nulls[$count] == true) ? "" : " NOT NULL";
               $subquery = implode(" ", $field) . $null;

               // the after attribute in a query is supposed to appear towards the end of a query, at least after the not null attribute
               // if both attribute exists in the query
               if (strpos($subquery, ' AFTER') != false && strpos($subquery, ' NOT NULL') != false) {
                  // and the position of the not null appears after the after attribute
                  if (strpos($subquery, ' NOT NULL') > strpos($subquery, ' AFTER')) {
                     // move the not null attribute to the current position of the after
                     // remove the current not null attribute and add it in the pos of the after attribute
                     $subquery = str_replace(' NOT NULL', '', $subquery);
                     $subquery = substr_replace($subquery, ' NOT NULL ', strpos($subquery, ' AFTER'), 1);
                  }
               }

               $query[] = $subquery;
               $count++;
            }
            foreach ($body->keys as $key) {
               $query[] = $key;
            }
            $sqlMode = $strict == false ? "SET SQL_MODE = ' '; " : "SET SQL_MODE = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION'; ";
            $sql = $sqlMode . "ALTER TABLE " . DB_PREFIX . $table . " " . implode(", ", $query) . " ";

            if ($_ENV['show_query'] == true) echo "\n" . $sql . "\n";

            $result = Model::query($sql);
            self::$resultArray[] = $result;
            echo $result == true ? "\n\tAlter $table: successful" : "\n\tAlter $table: failed ";
            self::$result = in_array(false, self::$resultArray) ? false : true;

            // clear properties
            self::clear($body);
         }
      } catch (\Throwable $ex) {
         trigger_error($ex->getMessage());
      }
   }

   public static function drop(string $table)
   {
      try {
         if (!empty($table)) {

            $sql = "DROP TABLE IF EXISTS " . DB_PREFIX . $table;

            if ($_ENV['show_query'] == true) echo "\n" . $sql . "\n";

            $result = Model::query($sql);
            self::$resultArray[] = $result;
            echo $result == true ? "\n\tDrop $table: successful" : "\n\tDrop $table: failed ";
            self::$result = in_array(false, self::$resultArray) ? false : true;
         }
      } catch (\Throwable $ex) {
         trigger_error($ex->getMessage());
      }
   }

   public static function rename(string $table, string $newtable, string $model, string $newmodel)
   {
      try {
         if (!empty($table) && !empty($newtable)) {

            $sql = "RENAME TABLE " . DB_DATABASE . ".$table TO " . DB_DATABASE . ".$newtable";

            if ($_ENV['show_query'] == true) echo "\n" . $sql . "\n";

            $result = Model::query($sql);
            self::$resultArray[] = $result;
            if ($result == true) {
               // rename the model class and the model file
               (new \Library\Console\Init())->_init_rename_model($table, $newtable, $model, $newmodel);
               echo "\n\tRename $table: successful";
            } else {
               echo "\n\tRename $table: failed ";
            };
            self::$result = in_array(false, self::$resultArray) ? false : true;
         }
      } catch (\Throwable $ex) {
         trigger_error($ex->getMessage());
      }
   }

   public static function truncate(string $table)
   {
      try {
         if (!empty($table)) {

            $sql = "TRUNCATE " . DB_PREFIX . $table;

            if ($_ENV['show_query'] == true) echo "\n" . $sql . "\n";

            $result = Model::query($sql);
            self::$resultArray[] = $result;
            echo $result == true ? "\n\tTruncate $table: successful" : "\n\tTruncate $table: failed ";
            self::$result = in_array(false, self::$resultArray) ? false : true;
         }
      } catch (\Throwable $ex) {
         trigger_error($ex->getMessage());
      }
   }

   public static function seed(string $table, array ...$inputs)
   {
      try {
         if (!empty($table)) {

            Model::$table = $table;

            $result = Model::createMany(...$inputs);
            self::$resultArray[] = $result;
            echo $result == true ? "\n\tSeed $table: successful" : "\n\tSeed $table: failed ";
            self::$result = in_array(false, self::$resultArray) ? false : true;
         }
      } catch (\Throwable $ex) {
         trigger_error($ex->getMessage());
      }
   }

   private static function clear($body)
   {
      $body->change = "";
      $body->open = false;
      $body->fields = [];
      $body->nulls = [];
      $body->field_index = -1;
      $body->field;
      $body->keys = [];
      $body->key_index = 0;
   }
   //

   // Datatypes Section

   // When altering fields
   public function change(string $field)
   {
      // field to be altered
      $this->change = "CHANGE $field ";
      return $this;
   }

   // when altering tables
   public function add()
   {
      $this->change = "ADD ";
      return $this;
   }

   // when altering tables
   public function dropfield(string $field)
   {
      // increment the field count
      $this->field_index++;
      // field to be dropped
      $this->fields[$this->field_index][] = "DROP $field ";
      // No nulls
      $this->nulls[$this->field_index] = true;
      // this is expected to be a single query operation
      return $this;
   }

   // Numeric
   // --------------------------------------------------------------
   public function int(string $name, int $size = 10)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name INT($size)";
      }

      return $this;
   }

   public function tiny_int(string $name, int $size = 10)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name TINYINT($size)";
      }

      return $this;
   }

   public function small_int(string $name, int $size = 10)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name SMALLINT($size)";
      }

      return $this;
   }

   public function medium_int(string $name, int $size = 10)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name MEDIUMINT($size)";
      }

      return $this;
   }

   public function big_int(string $name, int $size = 10)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name BIGINT($size)";
      }

      return $this;
   }

   public function decimal(string $name, string $size = '0,0')
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name DECIMAL($size)";
      }

      return $this;
   }

   public function double(string $name, string $size = '0,0')
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name DOUBLE($size)";
      }

      return $this;
   }

   public function float(string $name, string $size = '0,0')
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name FLOAT($size)";
      }

      return $this;
   }

   public function real(string $name, string $size = '0,0')
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name REAL($size)";
      }

      return $this;
   }

   public function bit(string $name, int $size = 1)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name BIT($size)";
      }

      return $this;
   }

   public function boolean(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name BOOLEAN";
      }

      return $this;
   }

   public function serial(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name SERIAL";
      }

      return $this;
   }
   // --------------------------------------------------------------

   // String
   // --------------------------------------------------------------
   public function varchar(string $name, int $size = 10)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name VARCHAR($size)";
      }

      return $this;
   }

   public function char(string $name, int $size = 10)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name CHAR($size)";
      }

      return $this;
   }

   public function tiny_text(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name TINYTEXT";
      }

      return $this;
   }

   public function text(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name TEXT";
      }

      return $this;
   }

   public function medium_text(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name MEDIUMTEXT";
      }

      return $this;
   }

   public function long_text(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name LONGTEXT";
      }

      return $this;
   }

   public function tiny_blob(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name TINYBLOB";
      }

      return $this;
   }

   public function medium_blob(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name MEDIUMBLOB";
      }

      return $this;
   }

   public function blob(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name BLOB";
      }

      return $this;
   }

   public function long_blob(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name LONGBLOB";
      }

      return $this;
   }

   public function binary(string $name, int $size = 10)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name BINARY($size)";
      }

      return $this;
   }

   public function varbinary(string $name, int $size = 10)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name VARBINARY($size)";
      }

      return $this;
   }

   public function enum(string $name, array $values)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $values = implode("','", $values);
         $this->fields[$this->field_index][] = $this->change . "$name ENUM('$values')";
      }

      return $this;
   }

   public function set(string $name, array $values)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $values = implode("','", $values);
         $this->fields[$this->field_index][] = $this->change . "$name SET('$values')";
      }

      return $this;
   }
   // --------------------------------------------------------------

   // Date and Time
   // --------------------------------------------------------------
   public function date(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name DATE";
      }

      return $this;
   }

   public function datetime(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name DATETIME";
      }

      return $this;
   }

   public function timestamp(string $name, bool $onUpdateCurrentTimestamp = true)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
         $this->fields[$this->field_index][] = $onUpdateCurrentTimestamp == true ? "ON UPDATE CURRENT_TIMESTAMP" : "";
      }

      return $this;
   }

   public function time(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name TIME";
      }

      return $this;
   }

   public function year(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name YEAR";
      }

      return $this;
   }
   // --------------------------------------------------------------

   // MISC
   public function json(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = $this->change . "$name JSON";
      }

      return $this;
   }
   // 

   // Attribute Section

   // After
   // when altering tables
   public function after(string $field)
   {
      // restrict from being the first attribute
      if ($this->open == false) {
         return $this;
      }
      $this->fields[$this->field_index][] = "AFTER $field";
      return $this;
   }

   // Default
   public function default(string $default = "NONE")
   {

      // restrict from being the first attribute
      if ($this->open == false) {
         return $this;
      }

      // Logic
      // the acceptable default value can be any value in the $accepted, 
      // or your own defined default. 
      // Any value not in the $accepted, would be taken as your defined default 
      // and wrapped inside a single quote.

      $accepted = ["NONE", "NULL", "CURRENT_TIMESTAMP"];

      if ($default != "NONE") {

         // some field types cannot have defaults
         // TINYTEXT, TEXT, MEDIUMTEXT, LONGTEXT

         $currentField = $this->fields[$this->field_index];
         if (!in_array("TINYTEXT", $currentField) && !in_array("TINYTEXT", $currentField) && !in_array("TINYTEXT", $currentField) && !in_array("TINYTEXT", $currentField)) {
            $this->fields[$this->field_index][] = (in_array($default, $accepted) == true) ? "DEFAULT $default" : "DEFAULT '$default'";
         }
      }

      return $this;
   }

   // Attribute
   public function attribute(string $attribute = "")
   {
      // restrict from being the first attribute
      if ($this->open == false) {
         return $this;
      }

      $accepted = ["", "BINARY", "UNSIGNED", "UNSIGNED ZEROFILL", "ON UPDATE CURRENT_TIMESTAMP"];

      if (in_array($attribute, $accepted)) {
         $this->fields[$this->field_index][] = "$attribute";
      }

      return $this;
   }

   // Not Null
   public function not_nullable()
   {
      // restrict from being the first attribute
      if ($this->open == false) {
         return $this;
      }

      $this->nulls[$this->field_index] = false;
      return $this;
   }

   public function nullable()
   {
      // restrict from being the first attribute
      if ($this->open == false) {
         return $this;
      }

      $this->nulls[$this->field_index] = true;
      return $this;
   }

   // Auto Increment
   public function auto_increment()
   {
      // restrict from being the first attribute
      if ($this->open == false) {
         return $this;
      }

      $this->fields[$this->field_index][] = "AUTO_INCREMENT";
      return $this;
   }

   // Comment
   public function comment(string $comment = "I love Initframework")
   {
      // restrict from being the first attribute
      if ($this->open == false) {
         return $this;
      }

      $this->fields[$this->field_index][] = "COMMENT '$comment'";
      return $this;
   }
   //

   // key Section

   // Primary Key
   public function primary()
   {
      $this->key_index++;
      $this->keys[$this->key_index] = "PRIMARY KEY (" . $this->field . ")";
      // $this->fields[$this->field_index][] = "PRIMARY KEY";
      return $this;
   }

   // Index Key
   public function index()
   {
      $this->key_index++;
      $this->keys[$this->key_index] = "INDEX (" . $this->field . ")";
      // $this->fields[$this->field_index][] = "INDEX ('" . $this->field . "')";
      return $this;
   }

   // Unique Key
   public function unique()
   {
      $this->key_index++;
      $this->keys[$this->key_index] = "UNIQUE (" . $this->field . ")";
      // $this->fields[$this->field_index][] = "UNIQUE";
      return $this;
   }

   // Foreign key
   public function foreign(string $local_field, string $foreign_table, string $foreign_field, string $on_delete = "ON DELETE NO ACTION", string $on_update = "ON UPDATE NO ACTION")
   {
      $this->key_index++;
      $accepted_onupdate = ["ON UPDATE NO ACTION", "ON UPDATE RESTRICT", "ON UPDATE CASCADE", "ON UPDATE SET NULL"];
      $accepted_ondelete = ["ON DELETE NO ACTION", "ON DELETE RESTRICT", "ON DELETE CASCADE", "ON DELETE SET NULL"];

      if (in_array($on_delete, $accepted_ondelete) && in_array($on_update, $accepted_onupdate)) {
         // DO NOT ALTER THIS!
         $constraint = 'CONSTRAINT ' . DB_DATABASE . '_' . DB_PREFIX . $foreign_table . '_fk_' . $foreign_field;
         $this->keys[$this->key_index] = "$constraint FOREIGN KEY ($local_field) REFERENCES " . DB_PREFIX . "$foreign_table($foreign_field) $on_update $on_delete";
      }

      return $this;
   }
   //

   public function __destruct()
   {
      self::$schema = $this;
   }
}
