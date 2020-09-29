<?php
namespace App;
use Library\Http\Http;
use Library\Http\Request;
use Library\Http\Response;
use Library\Http\Router;
use App\Middlewares\EchoHello;
use Controllers\Newcontroller;
use Controllers\Users\Usercontroller;

// use App\Middlewares\SessionAuth;

// set_include_path(APPLICATION_DIR)

// Include autoload for composer packages
include_once '../vendor/autoload.php';

// Setup Configurations
include_once '../app/config.php';

// Start 🚀🚀🚀
$req = new Request();

# ⬅⬆ 🚦 Router ⬇➡ #
Router::init($req);

$EchoHello = new EchoHello($req);

Router::get('/', $EchoHello, new Newcontroller($req));

Router::get('/users/{userId}', $EchoHello, new Usercontroller($req));

# 🚧🚧🚧🚧🚧🚧 #
Router::resolve();