<?php
namespace Controllers;
use Library\Http\Request;
use Library\Http\Response;

class Newcontroller
{

   public function __invoke(Request $req)
   {
      if ($req->requestUri == '/') {
         echo "Welcome to Home";
      } else {
         echo "Go to <a href='/'>Home</a>";
      }
   }

}
