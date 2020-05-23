<?php
namespace Framework\Gulp;
use Framework\Gulp\Migration;
use Framework\Gulp\Controller;
use Framework\Gulp\Model;
use Framework\Gulp\View;

class Init
{

   public function __construct($args)
   {
      // Set Timezone
      date_default_timezone_set(TIMEZONE);
      $this->cli($args);
   }

   public function cli($args)
   {
      $command = $args[1];
      switch ($command) {
         case 'create':
            // get options
            $option = $args[2];
            switch ($option) {
               case 'migration':
                  $migration = $args[3];
                  Migration::_make($migration);
                  break;
               case 'model':
                  $model = $args[3];
                  $table = $args[4];
                  Model::_make($model, $table);
                  break;
               case 'controller':
                  $controller = $args[3];
                  Controller::_make($controller);
                  break;
               case 'view':
                  $view = $args[3];
                  View::_make($view);
                  break;
               case 'lang':
               break;
               case 'secret-key':
                  echo \Framework\Cipher\AES::encrypt( uniqid(time(), true), APPLICATION ) . "\n";
                  break;
               default:
                  # code...
                  break;
            }
            break;
         
         case 'clone':
            // get options
            $option = $args[2];
            $src = $args[3];
            switch ($option) {
               case 'migration':
                  $migration = $args[4];
                  Migration::_clone($src, $migration);
                  break;
               case 'model':
                  $model = $args[4];
                  Model::_clone($src, $model);
                  break;
               case 'controller':
                  $controller = $args[4];
                  Controller::_clone($src, $controller);
                  break;
               case 'view':
                  $view = $args[4];
                  View::_clone($src, $view);
                  break;
               case 'lang':break;
               default:
                  # code...
                  break;
            }
            break;

         case 'run':
            // get options
            $option = $args[2];
            switch ($option) {
               case 'migration':
                  $filename = $args[3];
                  Migration::_run($filename);
                  break;
               case 'model':break;
               case 'controller':break;
               case 'view':break;
               case 'lang':break;
               default:
                  # code...
                  break;
            }
            break;

         case '-h':
            $msg = "Hi 👋\nWelcome to Init Framework.🚀🚀\n";
            echo $msg;
            
            break;
         default:
            # code...
            break;
      }

   }

}


// Migrations
// php init create migration dest
// php init clone migration src dest
// php init run migration src

// echo json_encode($args);

// php init command options args [--tags]
