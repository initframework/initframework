<?php
namespace Framework\Database;
use Framework\Database\Database;

class Designer extends Database
{

   private $conn;

   private $sql = "";
   private $open = false;

   private $fields = [];
   private $nulls = [];
   private $field_index = -1;
   private $table;
   private $field;

   private $keys = [];
   private $key_index = 0;

   public function __construct()
   {
      $this->conn = parent::__construct();
   }

   // Designer methods
   public function create(string $table)
   {
      // , bool $dropIfExists = false, ...$dropForeignKeys
      if (!empty($table)) {
         $this->table = $table;
         // if ($dropIfExists == true) {
         //    if ($dropForeignKeys != []) {
         //       foreach ($dropForeignKeys as $tableKey) {
         //          list($refTable, $key) = explode(".", $tableKey);
         //          $this->sql .= 'ALTER TABLE ' . DB_DATABASE . '.' . $refTable . ' DROP FOREIGN KEY ' . DB_DATABASE . '_' . $table . '_fk_' . $key . '; ';
         //       }
         //    }
         //    // CREATE TABLE `session` (
         //    $this->sql .= "DROP TABLE IF EXISTS $table; ";
         // }
         $this->sql .= "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . $table;
         echo "\ncompiling query...\n";
      }
   }

   // Datatypes Section

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
         $this->fields[$this->field_index][] = "$name INT($size)";
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
         $this->fields[$this->field_index][] = "$name TINYINT($size)";
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
         $this->fields[$this->field_index][] = "$name SMALLINT($size)";
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
         $this->fields[$this->field_index][] = "$name MEDIUMINT($size)";
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
         $this->fields[$this->field_index][] = "$name BIGINT($size)";
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
         $this->fields[$this->field_index][] = "$name DECIMAL($size)";
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
         $this->fields[$this->field_index][] = "$name DOUBLE($size)";
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
         $this->fields[$this->field_index][] = "$name FLOAT($size)";
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
         $this->fields[$this->field_index][] = "$name REAL($size)";
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
         $this->fields[$this->field_index][] = "$name BIT($size)";
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
         $this->fields[$this->field_index][] = "$name BOOLEAN";
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
         $this->fields[$this->field_index][] = "$name SERIAL";
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
         $this->fields[$this->field_index][] = "$name VARCHAR($size)";
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
         $this->fields[$this->field_index][] = "$name CHAR($size)";
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
         $this->fields[$this->field_index][] = "$name TINYTEXT";
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
         $this->fields[$this->field_index][] = "$name TEXT";
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
         $this->fields[$this->field_index][] = "$name MEDIUMTEXT";
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
         $this->fields[$this->field_index][] = "$name LONGTEXT";
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
         $this->fields[$this->field_index][] = "$name TINYBLOB";
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
         $this->fields[$this->field_index][] = "$name MEDIUMBLOB";
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
         $this->fields[$this->field_index][] = "$name BLOB";
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
         $this->fields[$this->field_index][] = "$name LONGBLOB";
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
         $this->fields[$this->field_index][] = "$name BINARY($size)";
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
         $this->fields[$this->field_index][] = "$name VARBINARY($size)";
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
         $this->fields[$this->field_index][] = "$name ENUM('$values')";
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
         $this->fields[$this->field_index][] = "$name SET('$values')";
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
         $this->fields[$this->field_index][] = "$name DATE";
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
         $this->fields[$this->field_index][] = "$name DATETIME";
      }

      return $this;
   }

   public function timestamp(string $name)
   {
      // increment the field count
      $this->field_index++;
      // indicate that other options can now be passed
      $this->open = true;

      if (!empty($name)) {
         // Current field 
         $this->field = $name;
         $this->fields[$this->field_index][] = "$name TIMESTAMP";
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
         $this->fields[$this->field_index][] = "$name TIME";
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
         $this->fields[$this->field_index][] = "$name YEAR";
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
         $this->fields[$this->field_index][] = "$name JSON";
      }

      return $this;
   }

   // Attribute Section
   // --------------------------------------------------------------

   // Default
   public function default(string $default = "NONE")
   {

      // restrict from being the first attribute
      if ($this->open == false){
         return $this;
      }

      // Logic
      // the acceptable default value can be any value in the $accepted, 
      // or your own defined default. 
      // Any value not in the $accepted, would be taken as your defined default 
      // and wrapped inside a single quote.
   
      $accepted = ["NONE", "NULL", "CURRENT_TIMESTAMP"];

      if ($default != "NONE") {
         $this->fields[$this->field_index][] = (in_array($default, $accepted) == true) ? "DEFAULT $default" : "DEFAULT '$default'";
      }

      return $this;
   }

   // Attribute
   public function attribute(string $attribute = "")
   {
      // restrict from being the first attribute
      if ($this->open == false){
         return $this;
      }

      $accepted = ["", "BINARY", "UNSIGNED", "UNSIGNED ZEROFILL", "ON UPDATE CURRENT_TIMESTAMP"];

      if (in_array($attribute, $accepted)) {
         $this->fields[$this->field_index][] = "$attribute";
      }

      return $this;
   }

   // Not Null
   public function not_null()
   {
      // restrict from being the first attribute
      if ($this->open == false){
         return $this;
      }

      $this->nulls[$this->field_index] = false;
      return $this;
   }

   public function null()
   {
      // restrict from being the first attribute
      if ($this->open == false){
         return $this;
      }

      $this->nulls[$this->field_index] = true;
      return $this;
   }
   
   // Auto Increment
   public function auto_increment()
   {
      // restrict from being the first attribute
      if ($this->open == false){
         return $this;
      }

      $this->fields[$this->field_index][] = "AUTO_INCREMENT";
      return $this;
   }

   // Comment
   public function comment(string $comment = "I love Initframework")
   {
      // restrict from being the first attribute
      if ($this->open == false){
         return $this;
      }

      $this->fields[$this->field_index][] = "COMMENT '$comment'";
      return $this;
   }

   // --------------------------------------------------------------

   // key Section
   // --------------------------------------------------------------

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

   // Index Key
   public function unique()
   {
      $this->key_index++;
      $this->keys[$this->key_index] = "UNIQUE (" . $this->field . ")";
      // $this->fields[$this->field_index][] = "UNIQUE";
      return $this;
   }

   // Foreign key
   public function foreign(string $local_field, string $foreign_table, string $foreign_field, string $on_delete = "NO ACTION", string $on_update = "NO ACTION")
   {
      $this->key_index++;
      $accepted = ["NO ACTION", "RESTRICT", "CASCADE", "SET NULL"];

      if (in_array($on_delete, $accepted) && in_array($on_update, $accepted)) {
         $constraint = 'CONSTRAINT ' . DB_DATABASE . '_' . $foreign_table . '_fk_' . $foreign_field;
         $this->keys[$this->key_index] = "$constraint FOREIGN KEY ($local_field) REFERENCES $foreign_table($foreign_field) ON UPDATE $on_update ON DELETE $on_delete";
      }

      return $this;
   }


   // Execution

   public function exe()
   {
      // Create query for table
      $query = []; $count = 0;
      foreach ($this->fields as $field) {
         $null = (isset($this->nulls[$count]) && $this->nulls[$count] == true) ? "" : " NOT NULL" ;
         $query[] = implode(" ", $field) . $null;
         $count++;
      }
      foreach ($this->keys as $key) {
         $query[] = $key;
      }
      $this->sql .= " ( " . implode(", ", $query) . " ) ";

      echo "executing query:\n\n" . $this->sql;

      try {
         $stmt = $this->conn->prepare($this->sql);
         $result = $stmt->execute();
         if ($result) {
            echo "\n\nexecuted successfully!\n";
         } else {
            throw new PDOException("\n\nexecution failed!\n", 1);
         }
      }
      catch(PDOException $e) {
         // echo $e->getMessage();
      }
   
      // clear properties
      $this->clear();

   }

   private function clear()
   {
      $this->sql = "";
      $this->open = false;

      $this->fields = [];
      $this->nulls = [];
      $this->field_index = -1;
      $this->table;
      $this->field;

      $this->keys = [];
      $this->key_index = 0;
   }


}
