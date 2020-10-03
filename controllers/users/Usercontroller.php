<?php
namespace Controllers\Users;
use Library\Http\Request;

class Usercontroller
{

   public function __invoke(Request $req)
   {
      extract($req->routeParams);
      if ($req->requestUri == '/') {
         echo "Welcome to Home" . $userId;
      } else {
         echo "Go to {$userId} <a href='/'>Home</a>";
      }
   }

}
