<?php
namespace Services;

class Sanitize
{
   // any
   public static function any(string $value)
   {
      return filter_var($value, FILTER_DEFAULT);
   }

   // string
   public static function string(string $value)
   {
      return filter_var($value, FILTER_SANITIZE_STRING, [FILTER_FLAG_STRIP_HIGH]);
   }

   // email
   public static function email(string $value)
   {
      return filter_var($value, FILTER_SANITIZE_EMAIL);
   }

   // encoded
   public static function encoded(string $value)
   {
      return filter_var($value, FILTER_SANITIZE_ENCODED, [FILTER_FLAG_STRIP_HIGH, FILTER_FLAG_STRIP_LOW]);
   }

   // amount
   public static function decimal(string $value)
   {
      return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, [FILTER_FLAG_ALLOW_FRACTION, FILTER_FLAG_ALLOW_THOUSAND]);
   }

   // numbers
   public static function numbers(string $value)
   {
      return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
   }

   // specialchar
   public static function specialchar(string $value)
   {
      return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
   }

   // url
   public static function url(string $value)
   {
      return filter_var($value, FILTER_SANITIZE_URL);
   }

}