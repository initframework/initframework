<?php
namespace Framework\Gulp;
class Model
{
   public function __construct()
   {

   }

   public static function _make(string $model, string $table) : void
   {
      // can i describe the table in the code?
      $model = ucfirst($model);
      $code = 
<<<PHP
<?php
namespace Models;
use Framework\Database\Model;

class $model extends Model
{
   public function __construct()
   {
      parent::__construct('$table');
   }

   // write wonderful model codes...

}

PHP;

      $file = APPLICATION_DIR . "models/$model.php";
      if (\file_exists($file)) {
         echo "Error: $model.php already exists!";
         return;
      } else {
         $newfile = fopen($file, "w");
         fwrite($newfile, $code);
         fclose($newfile);
         echo "Success: $model.php created successfully!";
         return;
      }
   }

   public static function _clone(string $src, string $model)
   {
      $srcfile = APPLICATION_DIR . "models/$src.php";
      $model = ucfirst($model);

      if (!\file_exists($src)) {
         echo "Error: $src.php does not exist!";
         return;
      }
      $code = \file_get_contents($srcfile);

      $file = APPLICATION_DIR . "models/$model.php";
      if (\file_exists($file)) {
         echo "Error: $model.php already exists!";
         return;
      } else {
         $newfile = fopen($file, "w");
         fwrite($newfile, $code);
         fclose($newfile);
         echo "Success: $model.php created successfully!";
         return;
      }
   }

   private static function _hash() 
   {
      $hex = ['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f'];
      $hash = "";
      for ($max = 7; $max > 0; $max--) {
         $i = rand(0,15);
         $hash .= $hex[$i];
      }
      return $hash;
   }

}