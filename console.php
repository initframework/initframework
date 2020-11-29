<?php
namespace Console;
use Library\Http\Request;
use Library\Http\Router;

// autoloader
set_include_path(\dirname(__DIR__) . '/');
include_once 'vendor/autoload.php';

// 🚀🚀🚀
$req = new Request();

// restrict access to console only
if (PHP_SAPI != 'cli') notFoundError($req);

// 🚦 Router
Router::start($req);

/**
 * A CRON command to perform the session garbage collection
 */
Router::command('gc', function() {
   // Executes GC immediately
   session_gc();

   // Clean up session ID created by session_gc()
   session_destroy();
});
