<?php
namespace Library\Gulp;
class Controller
{
   public function __construct()
   {

   }

   public static function _make(string $controller) : void
   {
      $controller = ucfirst($controller);
      $code = 
<<<PHP
<?php
namespace Controllers;
use Library\Http\Http;
use Library\Http\Request;
use Library\Http\Response;
use App\Auth;

class $controller
{

   public function index(Request \$req, Response \$res)
   {
      // return all resources
   }

   public function create(Request \$req, Response \$res)
   {
      // create a resource
   }

   public function read(Request \$req, Response \$res)
   {
      // return a resource
   }

   public function update(Request \$req, Response \$res)
   {
      // update a resource
   }

   public function delete(Request \$req, Response \$res)
   {
      // remove a resouce
   }

}

PHP;

      $file = APPLICATION_DIR . "controllers/$controller.php";
      if (\file_exists($file)) {
         echo "Error: $controller.php already exists!";
         return;
      } else {
         $newfile = fopen($file, "w");
         fwrite($newfile, $code);
         fclose($newfile);
         echo "Success: $controller.php created successfully!";
         return;
      }
   }

   public static function _clone(string $src, string $controller)
   {
      $controller = ucfirst($controller);
      $srcfile = APPLICATION_DIR . "controllers/$src.php";

      if (!\file_exists($src)) {
         echo "Error: $src.php does not exist!";
         return;
      }
      $code = \file_get_contents($srcfile);

      $file = APPLICATION_DIR . "controllers/$controller.php";
      if (\file_exists($file)) {
         echo "Error: $controller.php already exists!";
         return;
      } else {
         $newfile = fopen($file, "w");
         fwrite($newfile, $code);
         fclose($newfile);
         echo "Success: $controller.php created successfully!";
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