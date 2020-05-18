<?php 

namespace Test;
use Framework\Http\Http;
use Framework\Http\Request;
use Framework\Http\Response;

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