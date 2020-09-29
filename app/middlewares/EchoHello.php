<?php
namespace App\Middlewares;
use Library\Http\Request;

class EchoHello
{
   public function __invoke(Request $request)
   {
      echo "Hello World<br>";
   }
}
