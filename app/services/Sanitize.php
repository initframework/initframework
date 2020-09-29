<?php
namespace App\Services;

class Sanitize
{
   // string
   public function string(string $value)
   {
      return filter_var($value, FILTER_SANITIZE_STRING, [FILTER_FLAG_STRIP_HIGH]);
   }

   // email
   public function email(string $value)
   {
      return filter_var($value, FILTER_SANITIZE_EMAIL);
   }

   // encoded
   public function encoded(string $value)
   {
      return filter_var($value, FILTER_SANITIZE_ENCODED, [FILTER_FLAG_STRIP_HIGH, FILTER_FLAG_STRIP_LOW]);
   }

   // amount
   public function amount(string $value)
   {
      return filter_var($value, FILTER_SANITIZE_FLOAT, [FILTER_FLAG_ALLOW_FRACTION, FILTER_FLAG_ALLOW_THOUSAND]);
   }

   // numbers
   public function numbers(string $value)
   {
      return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
   }

   // specialchar
   public function specialchar(string $value)
   {
      return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
   }

   // url
   public function url(string $value)
   {
      return filter_var($value, FILTER_SANITIZE_URL);
   }

}