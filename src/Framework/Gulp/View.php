<?php
namespace Framework\Gulp;
use Framework\Handler\IException;
class View
{
   public function __construct()
   {

   }

   public static function _make(string $view) : void
   {
      // NOTE: Accept tags for templating views
      $code = 
<<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <!-- SEO Meta Tags -->
   <title>Init Application</title>

   <!-- Import Assets -->
   <!-- JQuery for Faster Scripting -->
   <script src="@assets/js/jquery.min.js"></script>
   <script src="@assets/js/popper.min.js"></script>
   <!-- Bootstrap for Faster Styling -->
   <script src="@assets/js/bootstrap.min.js"></script>
   <link rel="stylesheet" href="@assets/css/bootstrap.css">
   <!-- FontAwesome for Your Icons -->
   <link rel="stylesheet" href="@assets/css/fontawesome.css">
   <!-- Your Custom Assets -->
   <script src="@assets/js/main.js\"></script>
   <link rel="stylesheet" href="@assets/css/main.css">
</head>
<body>

   <!-- start buiding that nice interface... 😉 -->

</body>
</html>
HTML;

      try {
         $file = TEMPLATE_DIR . "$view.html";
         if (\file_exists($file)) {
            throw new IException("Error: $view.html already exists!");
            return;
         } else {
            if (!@fopen($file, "w")) {
               throw new IException("Error: Can't open file at $file");
            }
            $newfile = @fopen($file, "w");
            fwrite($newfile, $code);
            fclose($newfile);
            exit("Success: $view.html created successfully!\n");
         }
      } catch (IException $ex) {
         $ex->handle('cli');
      }

   }

   public static function _clone(string $src, string $view)
   {
      $srcfile = TEMPLATE_DIR . "$src.html";

      if (!\file_exists($src)) {
         echo "Error: $src.html does not exist!";
         return;
      }
      $code = \file_get_contents($srcfile);

      $file = TEMPLATE_DIR . "$view.html";
      if (\file_exists($file)) {
         echo "Error: $view.html already exists!";
         return;
      } else {
         $newfile = fopen($file, "w");
         fwrite($newfile, $code);
         fclose($newfile);
         echo "Success: $view.html created successfully!";
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