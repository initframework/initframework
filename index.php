<?php
namespace App;
use Library\Http\Request;
use Library\Http\Router;
use App\Middlewares\EchoHello;
use Controllers\Newcontroller;
use Controllers\Users\Usercontroller;

// Include autoload for composer packages
include_once 'vendor/autoload.php';
// Setup Configurations
include_once 'app/config.php';
// Start 🚀🚀🚀
$req = new Request();
# ⬅⬆ 🚦 Router ⬇➡ #
Router::init($req);
# Middlewares 🤚🚫🛑
$EchoHello = new EchoHello($req);

Router::get('/html', function($req) {
   html('welcome.html');
});

Router::get('/render', function($req) {
   render('welcome.html', [
      "myapp" => "Init Framework"
   ]);
});

Router::get('/getform', function($req) {
   render('/submitform.html');
});

Router::put('/submitput', function($req) {
   extract($req->body);
   echo <<<HTML
   Your name is $name
HTML;
});

Router::get('/errorhandler', function($req) {
   echo $rem;
});

Router::get('/', $EchoHello, new Newcontroller($req));

Router::get('/users', function(Request $req) {
   success("Hello");
});

Router::get('/users/{userId}', $EchoHello, new Usercontroller($req));

# 🚧🚧🚧🚧🚧🚧 #
Router::resolve();