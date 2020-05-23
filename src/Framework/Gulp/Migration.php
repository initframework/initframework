<?php
namespace Framework\Gulp;
use Framework\Handler\IException;
class Migration
{
   public function __construct()
   {

   }

   public static function _make(string $migration)
   {
      $code = 
<<<PHP
<?php
namespace App\Migrations;
use Framework\Database\Designer;

\$table = new Designer();



/*
   Help

   To create a table
   \$table->create('table-name');

   To drop a table if it exists before creating it
   \$table->create('table-name', true);

   To drop a table that references foreign keys before creating it
   \$table->create('table-name', true, 'referencing-table-name.referenced-key');
*/
PHP;

      try {
         $file = APPLICATION_DIR . "app/migrations/$migration.php";
         if (\file_exists($file)) {
            throw new IException("Error: $migration.php already exists!");
            return;
         } else {
            if (!@fopen($file, "w")) {
               throw new IException("Error: Can't open file at $file");
            }
            $newfile = @fopen($file, "w");
            fwrite($newfile, $code);
            fclose($newfile);
            exit("Success: $migration.php created successfully!\n");
         }
      } catch (IException $ex) {
         $ex->handle('cli');
      }
      
   }

   public static function _clone(string $src, string $migration)
   {
      try {
         $srcfile = APPLICATION_DIR . "app/migrations/$src.php";
         if (!\file_exists($srcfile)) {
            throw new IException("Error: $src.php does not exist!");
            return;
         }
         $code = \file_get_contents($srcfile);

         $file = APPLICATION_DIR . "app/migrations/$migration.php";
         if (\file_exists($file)) {
            throw new IException("Error: $migration.php already exists!");
            return;
         } else {
            if (!@fopen($file, "w")) {
               throw new IException("Error: Can't open file at $file");
            }
            $newfile = @fopen($file, "w");
            fwrite($newfile, $code);
            fclose($newfile);
            exit("Success: $migration.php created successfully!\n");
            return;
         }
      } catch (IException $ex) {
         $ex->handle('cli');
      }
      
   }

   public static function _run(string $filename)
   {
      try {
         $exefile = APPLICATION_DIR . "app/migrations/$filename.php";
         if (\file_exists($exefile) && is_file($exefile)) {
            include_once $exefile;
         } else {
            throw new IException("Error: $filename.php does not exist!");
         }
      } catch (IException $ex) {
         $ex->handle('cli');
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