<?php
namespace Framework\Database;
use Framework\Database\Model;

class Designer extends Model
{

   private $sql = "";
   private $open = false;

   private $fields = [];
   private $field_index = -1;
   private $table;
   private $field;

   private $alter_sql = [];
   private $alter_index = 0;

   // Designer methods
   public function create(string $table, $dropIfExist)
   {
      if (!empty($table)) {
         $this->table = $table;
         $this->sql .= "CREATE TABLE $table";
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

   public function decimal(string $name, float $size = 10.0)
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

   public function double(string $name, float $size = 10.0)
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

   public function float(string $name, float $size = 10.0)
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

   public function real(string $name, float $size = 10.0)
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

      $this->fields[$this->field_index][] = "NOT NULL";
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
      $this->alter_index++;
      $this->alter_sql[$this->alter_index] = "ADD PRIMARY KEY ('" . $this->field . "')";
      return $this;
   }

   // Primary Key
   public function index()
   {
      $this->alter_index++;
      $this->alter_sql[$this->alter_index] = "ADD INDEX KEY ('" . $this->field . "')";
      return $this;
   }

   // Foreign key
   public function foreign_key(string $local_field, string $foreign_table, string $foreign_field, string $on_update = "NO ACTION", string $on_delete = "NO ACTION")
   {
      $this->alter_index++;
      $accepted = ["NO ACTION", "RESTRICT", "CASCADE", "SET NULL"];

      if (in_array($on_delete, $accepted) && in_array($on_update, $accepted)) {
         
         $this->fields[$this->alter_index] = "ADD FOREIGN KEY ($local_field) REFERENCES $foreign_table($foreign_field) ON DELETE $on_delete ON UPDATE $on_update, ";
         
      }

      return $this;
   }


   // Execution

   public function exe()
   {
      // Create query for table
      $fields = [];
      foreach ($this->fields as $field) {
         $fields[] = implode(" ", $field);
      }
      $this->sql .= " ( " . implode(", ", $fields) . " ) ";

      // Alter query for table keys
      $alter = "";
      if ($this->alter_index > 0) {
         $alter .= "ALTER TABLE '{$this->table}' ";
         $alter .= implode(", ", $this->alter_sql);
      }


      echo $this->sql;
      echo "\n\n";
      echo $alter;

      $this->sql = "";
      $this->fields = [];
      $this->field_index = -1;
      $this->open = false;
   }


}
