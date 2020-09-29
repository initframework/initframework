<?php 

namespace Test;
use Library\Http\Http;
use Library\Http\Request;
use Library\Http\Response;

class Test
{
   public function __construct() {
      include '../vendor/autoload.php';
      $http = new Http();

      $http->get('/', function (Request $req, Response $res) {
         $res->send("Helo World!");
      });
   }
}

(new Test())();