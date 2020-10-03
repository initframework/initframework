<?php
namespace Controllers;
use Library\Http\Request;

class Custom
{

   public static function index(Request $req)
   {
      exit('Hello Index');
   }

   public function create(Request $req)
   {
      // create a resource
   }

   public function read(Request $req)
   {
      // return a resource
   }

   public function update(Request $req)
   {
      // update a resource
   }

   public function delete(Request $req)
   {
      // remove a resouce
   }

}
