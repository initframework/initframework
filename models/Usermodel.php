<?php
namespace Models;
use Library\Database\Model;

class Usermodel extends Model
{
   public function __construct()
   {
      return parent::__construct('user_tbl');
   }

   // write wonderful model codes...

}
