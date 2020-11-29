<?php
namespace Middlewares;
use Library\Http\Request;
use Library\Http\Router;
use Services\User;

class Guard
{
   public function __construct(Request $request, string $privilege)
   {
      if (!User::has($privilege)) redirect(Router::getRoute('login'));
   }
}
