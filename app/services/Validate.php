<?php
namespace App\Services;

class Validate
{

   private $field = "";
   private $value = "";
   private $errmsg = "";
   private $errors = [];

   // any
   public function any(string $field, string $value, string $errmsg = "invalid field!")
   {
      $this->field = $field; $this->value = $value; $this->errmsg = $errmsg;
      return $this;
   }

   // numbers
   public function numbers(string $field, string $value, string $errmsg = "invalid number!")
   {
      $this->field = $field; $this->value = $value; $this->errmsg = $errmsg;
      if (filter_var($value, FILTER_VALIDATE_INT, [FILTER_FLAG_ALLOW_OCTAL, FILTER_FLAG_ALLOW_HEX]) == false) {
         $this->errors[$field] = $errmsg;
      }
      return $this;
   }

   // amount
   public function amount(string $field, string $value, string $errmsg = "invalid amount!")
   {
      $this->field = $field; $this->value = $value; $this->errmsg = $errmsg;
      if (filter_var($value, FILTER_VALIDATE_FLOAT, [FILTER_FLAG_ALLOW_THOUSAND]) == false) {
         $this->errors[$field] = $errmsg;
      }
      return $this;
   }

   // letters
   public function letters(string $field, string $value, string $errmsg = "invalid letters!")
   {
      $this->field = $field; $this->value = $value; $this->errmsg = $errmsg;
      if (preg_match("/[A-Za-z]+/", $value) == false) {
         $this->errors[$field] = $errmsg;
      }
      return $this;
   }

   // alphanumeric
   public function alphanumeric(string $field, string $value, string $errmsg = "invalid alphanumeric!")
   {
      $this->field = $field; $this->value = $value; $this->errmsg = $errmsg;
      if (preg_match("/[A-Z0-9a-z]+/", $value) == false) {
         $this->errors[$field] = $errmsg;
      }
      return $this;
   }

   // alphanumenricwildcards | password
   public function password(string $field, string $value, string $errmsg = "invalid password!")
   {
      $this->field = $field; $this->value = $value; $this->errmsg = $errmsg;
      if ( preg_match_all("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+}{\":;\'?\/>.<,])(?!.*\s).*$/", $value) == false ) {
         $this->errors[$field] = $errmsg;
      }
      return $this;
   }

   // email
   public function email(string $field, string $errmsg = "invalid email!")
   {
      $this->field = $field; $this->value = $value; $this->errmsg = $errmsg;
      if (filter_var($value, FILTER_VALIDATE_EMAIL, [FILTER_FLAG_EMAIL_UNICODE]) == false) {
         $this->errors[$field] = $errmsg;
      }
      return $this;
   }

   // telephone
   public function telephone(string $field, string $value, string $errmsg = "invalid telephone!")
   {
      $this->field = $field; $this->value = $value; $this->errmsg = $errmsg;
      if ( preg_match("/^[0-9-+()]+$/", preg_replace("/\s/", "", $value)) == false ){
         $this->errors[$field] = $errmsg;
      }
      return $this;
   }

   // ip
   public function ip(string $field, string $value, string $errmsg = "invalid ip address!")
   {
      $this->field = $field; $this->value = $value; $this->errmsg = $errmsg;
      if (filter_var($value, FILTER_VALIDATE_IP, [FILTER_FLAG_IPV4, FILTER_FLAG_IPV6, FILTER_FLAG_NO_PRIV_RANGE, FILTER_FLAG_NO_RES_RANGE]) == false) {
         $this->errors[$field] = $errmsg;
      }
      return $this;
   }

   // mac
   public function mac(string $field, string $value, string $errmsg = "invalid mac address!")
   {
      $this->field = $field; $this->value = $value; $this->errmsg = $errmsg;
      if (filter_var($value, FILTER_VALIDATE_MAC) == false) {
         $this->errors[$field] = $errmsg;
      }
      return $this;
   }

   // url
   public function url(string $field, string $value, string $errmsg = "invalid url!")
   {
      $this->field = $field; $this->value = $value; $this->errmsg = $errmsg;
      if (filter_var($value, FILTER_VALIDATE_URL, [FILTER_FLAG_SCHEME_REQUIRED, FILTER_FLAG_HOST_REQUIRED, FILTER_FLAG_PATH_REQUIRED, FILTER_FLAG_QUERY_REQUIRED]) == false) {
         $this->errors[$field] = $errmsg;
      }
      return $this;
   }

   // domain
   public function domain(string $field, string $value, string $errmsg = "invalid domain!")
   {
      $this->field = $field; $this->value = $value; $this->errmsg = $errmsg;
      if (filter_var($value, FILTER_VALIDATE_DOMAIN, [FILTER_FLAG_HOSTNAME]) == false) {
         $this->errors[$field] = $errmsg;
      }
      return $this;
   }

   // date
   public function date(string $field, string $value, string $errmsg = "invalid date!")
   {
      $this->field = $field; $this->value = $value; $this->errmsg = $errmsg;
      if ( preg_match("/^[0-9-:\/WT]+$/", $value) == false ){
         $this->errors[$field] = $errmsg;
      }
      return $this;
   }

   // -----------

   // exact
   public function exact(int $length)
   {
      if ( strlen($this->value) != $length ){
         $this->errors[$this->field] = $this->errmsg;
      }
      return $this;
   }

   // max
   public function max(int $length)
   {
      if ( strlen($this->value) > $length ){
         $this->errors[$this->field] = $this->errmsg;
      }
      return $this;
   }

   // min
   public function min(int $length)
   {
      if ( strlen($this->value) < $length ){
         $this->errors[$this->field] = $this->errmsg;
      }
      return $this;
   }

   // required

   // ------------

   // errors
   public function errors()
   {
      return $this->errors;
   }

}

// passthru('touch gamer.txt');