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
use Library\Http\Request;

class $controller
{

   public function __invoke(Request \$req)
   {
      // return all resources
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