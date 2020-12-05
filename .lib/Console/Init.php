<?php
namespace Library\Console;
use Library\Database\Schema;
use Library\Database\Database;
use PDO;

class Init extends Database
{

   public function __construct(array $args = null)
   {
      if (!is_null($args)) {
         // Set Timezone
         date_default_timezone_set(TIMEZONE);
         $command = $args[1] ?? "-h";
         $component = $args[2] ?? null;
         $arg1 = $args[3] ?? null;
         $arg2 = $args[4] ?? null;
         $arg3 = $args[5] ?? null;
         echo "Initframework 2.0.0 by Ebuka Odini.\n\n";
         $this->handle($command, $component, [$arg1, $arg2, $arg3]);
      }
   }

   public function handle(string $command, $component, array $args) {
      $init = "./init";
      switch ($command) {
         case '--h':
         case 'help':
            $this->_init_full_help($init);
         break;

         case 'new':
            // switch components
            $componentName = $args[0];
            switch ($component) {
               case 'controller':
                  if (!preg_match("/([A-Z0-9a-z_\/])+/", $componentName)) exit("Invalid controller name ($componentName); only lower case, upper case, numbers and underscore allowed\n");
                  $this->_init_new_controller(ucfirst(str_replace(".php", "", $componentName)));
               break;

               case 'model':
                  if (!preg_match("/([A-Z0-9a-z_\/])+/", $componentName)) exit("Invalid model name ($componentName); only lower case, upper case, numbers and underscore allowed\n");
                  $database_table = $args[1] ?? null;
                  if (is_null($database_table)) exit("Error: database table is required\n");
                  $this->_init_new_model(ucfirst(str_replace(".php", "", $componentName)), $database_table);
               break;

               case 'view':
                  if (!preg_match("/([A-Z0-9a-z_\/])+/", $componentName)) exit("Invalid view name ($componentName); only lower case, upper case, numbers and underscore allowed\n");
                  $this->_init_new_view(str_replace(".html", "", $componentName));
               break;

               case 'middleware':
                  if (!preg_match("/([A-Z0-9a-z_\/])+/", $componentName)) exit("Invalid middleware name ($componentName); only lower case, upper case, numbers and underscore allowed\n");
                  $this->_init_new_middleware(ucfirst(str_replace(".php", "", $componentName)));
               break;

               case 'service':
                  if (!preg_match("/([A-Z0-9a-z_\/])+/", $componentName)) exit("Invalid service name ($componentName); only lower case, upper case, numbers and underscore allowed\n");
                  $this->_init_new_service(ucfirst(str_replace(".php", "", $componentName)));
               break;

               case 'migration':
                  if (!preg_match("/([A-Z0-9a-z_\/])+/", $componentName)) exit("Invalid migration name ($componentName); only lower case, upper case, numbers and underscore allowed\n");
                  $this->_init_new_migration(str_replace(".php", "", preg_replace("/ +/", "_", $componentName)));
               break;

               case 'key':
                  $this->_init_new_key();
               break;
               
               default:
                  $this->_init_help($init);
               break;
            }
         break;
         
         case 'run':
            // switch components
            $componentName = $args[0];
            switch ($component) {
               case 'app':
                  $this->_init_run_app();
               break;

               case 'migrations':
                  $this->_init_run_migrations();
               break;

               case 'migration':
                  $this->_init_run_migration(str_replace(".php", "", preg_replace("/ +/", "_", $componentName)));
               break;
               
               default:
                  $this->_init_help($init);
               break;
            }
         break;

         case 'update':
            // switch components
            $componentName = $args[0];
            switch ($component) {
               case 'snippet':
                  if (!preg_match("/([A-Z0-9a-z_\/])+/", $componentName)) exit("Invalid model name ($componentName); only lower case, upper case, numbers and underscore allowed\n");
                  $database_table = $args[1] ?? null;
                  if (is_null($database_table)) exit("Error: database table is required\n");
                  $this->_init_model_snippet(ucfirst(str_replace(".php", "", $componentName)), $database_table);
               break;

               default:
                  $this->_init_help($init);
               break;
            }
         break;

         default:
            $this->_init_help($init);
         break;
      }
      exit;
   }

   private function _init_run_app()
   {
      echo "starting application...\npress Ctrl + C to cancel\n\n";
      passthru("php -S " . str_replace("http://", "", SERVER));
      exit();
   }

   private function _init_run_migrations()
   {
      // create the schema migration table is none exist
      Schema::create("schema_migration", function(Schema $schema) {
         $schema->int('id')->auto_increment()->primary();
         $schema->varchar('migration', 100);
         $schema->timestamp('migrated_at');
      }, false);
      
      // get migration files
      $files = array_diff(scandir(APP_BASEDIR . "app/migrations/"), array('.', '..', '.gitignore'));
      $conn = parent::getInstance();
      $migrated = 0;
      foreach ($files as $file) {
         // strip .php
         $migration = str_replace(".php", "", $file);
         // if migration does not exist
         $stmt = $conn->prepare("SELECT * FROM " . DB_PREFIX . "schema_migration WHERE migration = '$migration'"); $stmt->execute();
         if ($stmt->rowCount() > 0) continue;
         $method = "migrate";
         include_once APP_BASEDIR . "app/migrations/$file";
         $namespace = "\\Migrations\\";
         $class = $namespace."migration_$migration";
         if (method_exists($class, $method)){
            echo "$migration: ";
            (new $class())->$method();
            $migrated++;
            // add migration to db
            $stmt = $conn->prepare("INSERT INTO " . DB_PREFIX . "schema_migration (migration) VALUES('$migration')"); $stmt->execute();
            echo "migrated successfully!\n";
         } else {
            echo "Method ($method) does not exist in $class.\n";
         }
         unset($func);
      }
      if ($migrated == 0) echo "Error: No migration was migrated;\nmigrations have either been migrated or no migration exists.\n";
      exit;
   }

   private function _init_run_migration($migrationname)
   {
      // create the schema migration table is none exist
      Schema::create("schema_migration", function(Schema $schema) {
         $schema->int('id')->auto_increment()->primary();
         $schema->varchar('migration', 100);
         $schema->timestamp('migrated_at');
      }, false);
      
      // get migration files
      $files = array_diff(scandir(APP_BASEDIR . "app/migrations/"), array('.', '..', '.gitignore'));
      $conn = parent::getInstance();
      foreach ($files as $file) {
         $filename = substr($file, 18);
         if ($filename != $migrationname . ".php") continue;
         // strip .php
         $migration = str_replace(".php", "", $file);
         // if migration does not exist
         $stmt = $conn->prepare("SELECT * FROM " . DB_PREFIX . "schema_migration WHERE migration = '$migration'"); $stmt->execute();
         if ($stmt->rowCount() > 0) continue;
         $func = "migrate";
         include_once APP_BASEDIR . "app/migrations/$file";
         $namespace = "\\Migrations\\";
         $func = $namespace.$func;
         if (function_exists($func)){
            echo "$migration: ";
            $func();
            // add migration to db
            $stmt = $conn->prepare("INSERT INTO " . DB_PREFIX . "schema_migration (migration) VALUES('$migration')"); $stmt->execute();
            exit("migrated successfully!\n");
         } else {
            exit("Function does not exit. $func\n");
         }
      }
      exit("Error: migration '$migrationname' was not migrated\n");
   }

   private function _init_help($init): void
   {
echo <<<cmd
Usage: $init [command] [component] [args...]

Error: Wrong command, find help
>_ $init help or --h

cmd;

   }

   private function _init_full_help($init): void
   {
echo <<<CLI
Usage: $init [command] [component] [args...]

>_ $init help or --h
help

>_ $init new controller <controller>
creates a new controller in controllers/ directory

>_ $init new model <model> <database-table>
creates a model for a database table in the models/ directory
Example: '$init new model users user_tbl' creates a model class for the table users_tbl; now you can interact with the users_tbl by calling the users class (\$users)

>_ $init new view <view>
creates a new view in the public/views/ directory
Example: '$init new view about-us' would create a view named about-us.html

>_ $init new migration "migration"
creates a new schema migration class in the app/migrations/ for creating and altering the database schema

>_ $init new middleware <middleware>
creates a middleware class in the app/middlewares/ directory to be used when routing

>_ $init new service <service>
creates a service class in the app/services/ directory to be used within the application

>_ $init new key
creates a new key that you can use for your application

>_ $init run app
starts your application
Example: '$init run app' starts your application

>_ $init run migrations
migrates schemas that have not been migrated

>_ $init run migration <migration>
migrates a specific migration class if it has not been migrated

>_ $init update snippet <model> <database-table>
update the snippet for a specific model

CLI;
   }

   private function _init_new_key()
   {
      $hash = hash_hmac('sha256', mt_rand(), uniqid(true));
      exit("$hash\n");
   }

   private function _init_new_view(string $view) : void
   {
      $code = 
<<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   
   <title>Document</title>
   <!-- import favicon asset -->
   <link rel="shortcut icon" href="{{ asset('/imgs/favicon.ico') }}" type="image/x-icon">
</head>
<body>
   <!-- import data variables -->
   @vars

   <!-- View codes... -->

</body>
</html>
HTML;

      $file = APP_BASEDIR . "app/views/$view.html";
      if (\file_exists($file)) {
         echo "Error: $view.html already exists!\n";
         return;
      } else {
         $newfile = fopen($file, "w");
         fwrite($newfile, $code);
         fclose($newfile);
         echo "Success: $view.html created successfully!\n";
         return;
      }
   }

   private function _init_new_service(string $service) : void
   {
      $code = 
<<<PHP
<?php
namespace Services;

class $service
{
   public function __construct()
   {
      // Services codes...
   }
}

PHP;

      $file = APP_BASEDIR . "app/services/$service.php";
      if (\file_exists($file)) {
         echo "Error: $service.php already exists!\n";
         return;
      } else {
         $newfile = fopen($file, "w");
         fwrite($newfile, $code);
         fclose($newfile);
         echo "Success: $service.php created successfully!\n";
         return;
      }
   }

   private function _init_new_middleware(string $middleware) : void
   {
      $code = 
<<<PHP
<?php
namespace Middlewares;
use Library\Http\Request;

class $middleware
{
   public function __construct(Request \$request)
   {
      // Middleware codes...
   }
}

PHP;

      $file = APP_BASEDIR . "app/middlewares/$middleware.php";
      if (\file_exists($file)) {
         echo "Error: $middleware.php already exists!\n";
         return;
      } else {
         $newfile = fopen($file, "w");
         fwrite($newfile, $code);
         fclose($newfile);
         echo "Success: $middleware.php created successfully!\n";
         return;
      }
   }

   private function _init_new_migration(string $migration) : void
   {
      $prefix = date("Y_m_d_His_");
      $code = 
<<<PHP
<?php
namespace Migrations;
use Library\Database\Schema;

class migration_$prefix$migration {

   function migrate()
   {
      Schema::create('$migration', function(Schema \$schema) {
         \$schema->int('id')->auto_increment()->primary();
         \$schema->timestamp('created_at')->attribute();
         \$schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
      }, false, '$migration');

      // Schema::seed('$migration', 
      //    [
      //       'field' => 'value',
      //       'field' => 'value',
      //    ],
      //    [
      //       'field' => 'value',
      //       'field' => 'value',
      //    ],
      //    ...
      // );

      // Schema::alter('$migration', function(Schema \$schema) {
      //    \$schema->change('id')->double('id');
      //    \$schema->change('created_at')->datetime('created_at');
      //    \$schema->change('updated_at')->datetime('updated_at');
      // }, false);

      // Schema::drop('$migration');
   }

}

PHP;

      $file = APP_BASEDIR . "app/migrations/$prefix$migration.php";
      if (\file_exists($file)) {
         echo "Error: $migration.php already exists!\n";
         return;
      } else {
         $newfile = fopen($file, "w");
         fwrite($newfile, $code);
         fclose($newfile);
         echo "Success: $prefix$migration created successfully!\n";
         return;
      }
   }

   public function _init_model_snippet(string $model, string $table) : void
   {
      $codeSnippets = file_get_contents(APP_BASEDIR . "/.vscode/init.code-snippets");
      $snippet = json_decode($codeSnippets, true);

      try {
         $conn = parent::getInstance();
         $stmt = $conn->prepare("DESCRIBE " . DB_PREFIX . $table);
         $stmt->execute();
         $stmt->setFetchMode(PDO::FETCH_ASSOC);
         $fields = $stmt->fetchAll();
   
         $snippet["$model create"] = [
            "scope" => "php",
            "prefix" => "$model" . "::create"
         ];
   
         $snippet["$model create many"] = [
            "scope" => "php",
            "prefix" => "$model" . "::createMany"
         ];
   
         $snippet["$model update"] = [
            "scope" => "php",
            "prefix" => "$model" . "::update"
         ];
   
         $snippet["$model delete"] = [
            "scope" => "php",
            "prefix" => "$model" . "::delete"
         ];
   
         $snippet["$model exist"] = [
            "scope" => "php",
            "prefix" => "$model" . "::exist"
         ];
   
         $snippet["$model findAll"] = [
            "scope" => "php",
            "prefix" => "$model" . "::findAll"
         ];
   
         $snippet["$model findOne"] = [
            "scope" => "php",
            "prefix" => "$model" . "::findOne"
         ];
   
         $snippet["$model findJoin"] = [
            "scope" => "php",
            "prefix" => "$model" . "::findJoin"
         ];
   
         $snippet["$model fields"] = [
            "scope" => "php",
            "prefix" => "$table."
         ];
   
         $count = 1;
         foreach($fields as $field) {
            // generate create, update, and condition
            $label = $field['Field'];
            $raw_labels[] = $label;
            $input_fields[] = "\r\t\"$label\" => $" . $count;
            $count++;
         }
   
         // create body
         $input_fields = implode(",", $input_fields) . "\r";
         $snippet["$model create"]['body'] = $model . "::create" . "([" . $input_fields . "]);";
   
         // create many body
         $snippet["$model create many"]['body'] = $model . "::createMany" . "([" . $input_fields . "],\r$" . "{" . $count .":[]}\r);";
   
         $raw_fields = implode(",", $raw_labels);
         $raw_fields_Str = implode(", ", $raw_labels);
   
         // exist body
         $snippet["$model exist"]['body'] = $model . "::exist" . "(\"WHERE $" . "{1|" . $raw_fields . "|" . "} = 1\");";
   
         // delete body
         $snippet["$model delete"]['body'] = $model . "::delete" . "(\"WHERE $" . "{1|" . $raw_fields . "|" . "} = 1\");";
   
         // findOne body
         $snippet["$model findOne"]['body'] = $model . "::findOne" . "(\"$" . "{1:" . $raw_fields_Str . "}\", \"WHERE $" . "{2|" . $raw_fields . "|" . "} = 1\");";
   
         // findAll body
         $snippet["$model findAll"]['body'] = $model . "::findAll" . "(\"$" . "{1:" . $raw_fields_Str . "}\", \"WHERE $" . "{2|" . $raw_fields . "|" . "} = 1\");";
   
         // find body
         $array_fields = json_encode($raw_labels);
         $snippet["$model findJoin"]['body'] = "// Note: When joining, specify fieldnames as tablename.fieldname;\r// Prefix the every tablename with DB_PREFIX\r\\$" . "prefix = DB_PREFIX;\r" . $model . "::findJoin" . "($" . "{1:\"{\\$" . "prefix\\}" . $table . ".$" . "{2|" . $raw_fields . "|}\"}, \"WHERE $" . "{3|" . $raw_fields . "|" . "} = 1\")\r\t->$" . "{4|leftJoin,rightJoin,innerJoin,fullJoin|}(\"{\\$" . "prefix\\}tablename\", \"{\\$" . "prefix\\}" . $table . ".$" . "{6|" . $raw_fields . "|} = {\\$" . "prefix\\}tablename.field\")\r\t->join();";
   
         // fields body
         $snippet["$model fields"]['body'] = "\". DB_PREFIX .\"" . $table . ".$" . "{1|" . $raw_fields . "|}";
   
         // update body
         $snippet["$model update"]['body'] = $model . "::update" . "([" . $input_fields . "]";
         $snippet["$model update"]['body'] .= ", \"WHERE $" . "{" . $count . "|" . $raw_fields . "|" . "} = 1\");";
   
         // update snippet file
         $ready_snippet = json_encode($snippet, JSON_PRETTY_PRINT);
         file_put_contents(APP_BASEDIR . "/.vscode/init.code-snippets", $ready_snippet);
   
         echo "updated model snippet for $model.php!\n";
         return;
      } catch (\Throwable $th) {
         exit(DB_PREFIX . $table . " does not exist in the database\n");
      }
   }

   public function _init_new_model(string $model, string $table): void
   {
      $model = ucfirst($model);
      $code = 
<<<PHP
<?php
namespace Models;
use Library\Database\Model;

class $model extends Model
{
   public static \$table = '$table';
   
   // Model codes...

}

PHP;

      $file = APP_BASEDIR . "app/models/$model.php";
      if (\file_exists($file)) {
         echo "Error: $model.php already exists!\n";
         return;
      } else {
         $newfile = fopen($file, "w");
         fwrite($newfile, $code);
         fclose($newfile);
         echo "Success: $model.php created successfully!\n";
         echo "Creating model snippet: ";
         // creating the model snippets
         @$this->_init_model_snippet($model, $table);
         return;
      }
   }


   private function _init_new_controller(string $controller) : void
   {
      $code = 
<<<PHP
<?php
namespace Controllers;
use Library\Http\Request;

class $controller
{

   public static function index(Request \$req)
   {
      // return all resources
   }

   public static function create(Request \$req)
   {
      // create a resource
   }

   public static function read(Request \$req)
   {
      // return a resource
   }

   public static function update(Request \$req)
   {
      // update a resource
   }

   public static function delete(Request \$req)
   {
      // remove a resouce
   }

}

PHP;

      $file = APP_BASEDIR . "app/controllers/$controller.php";
      if (\file_exists($file)) {
         echo "Error: $controller.php already exists!\n";
         return;
      } else {
         $newfile = fopen($file, "w");
         fwrite($newfile, $code);
         fclose($newfile);
         // append controller to index.php
         $indexFile = file_get_contents(APP_BASEDIR . "/index.php");
         $indexFileArr = explode("\n", $indexFile);
         $indexHead = array_slice($indexFileArr, 0, 4);
         $indexContent = array_slice($indexFileArr, 4);
         $indexContentReversed = array_reverse($indexContent);
         array_push($indexContentReversed, "use Controllers\\$controller;");
         $indexFile = implode("\n", array_merge($indexHead, array_reverse($indexContentReversed)));
         file_put_contents(APP_BASEDIR . "/index.php", $indexFile);
         // use Controllers\Music;
         echo "Success: $controller.php created successfully!\n";
         return;
      }
   }

}
