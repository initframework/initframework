<?php
namespace App\Middlewares;
use Library\Http\Request;

class SessionAuth
{
   public function __invoke(Request $request)
   {
      echo "Authenticated<br>";
   }
}