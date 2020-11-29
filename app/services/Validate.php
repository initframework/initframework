<?php
namespace Services;

class Validate
{

   public static $error = [];
   public static $status = true;
   
   // exact
   public static function hasExactLength(string $field, string $value, int $length)
   {
      if (strlen($value) != $length) {
         self::$status = false;
         self::$error[$field] = "$field must be exactly $length characters";
         return false;
      }
      return true;
   }

   // max
   public static function hasMaxLength(string $field, string $value, int $length)
   {
      if (strlen($value) > $length) {
         self::$status = false;
         self::$error[$field] = "$field cannot be more than $length characters";
         return false;
      }
      return true;
   }

   // min
   public static function hasMinLength(string $field, string $value, int $length)
   {
      if (strlen($value) < $length) {
         self::$status = false;
         self::$error[$field] = "$field cannot be less than $length characters";
         return false;
      }
      return true;
   }

   // number
   public static function isInteger(string $field, string $value)
   {
      if (filter_var($value, FILTER_VALIDATE_INT) == false) {
         self::$status = false;
         self::$error[$field] = "$field must be an integer";
         return false;
      }
      return true;
   }

   // decimal
   public static function isDecimal(string $field, string $value)
   {
      if (filter_var($value, FILTER_VALIDATE_FLOAT) == false) {
         self::$status = false;
         self::$error[$field] = "$field must be a decimal";
         return false;
      }
      return true;
   }

   // must contain numbers
   public static function mustContainNumber(string $field, string $value)
   {
      if (preg_match("/\d+/", $value) == false) {
         self::$status = false;
         self::$error[$field] = "$field must contain numbers";
         return false;
      }
      return true;
   }


   // must contain letters
   public static function mustContainLetters(string $field, string $value)
   {
      if (preg_match("/[A-Za-z]+/", $value) == false) {
         self::$status = false;
         self::$error[$field] = "$field must contain letters";
         return false;
      }
      return true;
   }

   // must contain upper-case letters
   public static function mustContainUpperCase(string $field, string $value)
   {
      if (preg_match("/[A-Z]+/", $value) == false) {
         self::$status = false;
         self::$error[$field] = "$field must contain upper-case letters";
         return false;
      }
      return true;
   }

   // must contain lower-case letters
   public static function mustContainLowerCase(string $field, string $value)
   {
      if (preg_match("/[a-z]+/", $value) == false) {
         self::$status = false;
         self::$error[$field] = "$field must contain lower-case letters";
         return false;
      }
      return true;
   }

   // email
   public static function isValidEmail(string $field, string $value)
   {
      if (filter_var($value, FILTER_VALIDATE_EMAIL) == false) {
         self::$status = false;
         self::$error[$field] = "$field is not a valid email";
         return false;
      }
      return true;
   }

   public function isValidPassword(string $field, string $value, bool $mustContainNumber = true, bool $mustContainLowerCase = true, bool $mustContainUpperCase = true, bool $mustContainSpecialChars = true, int $minlength = 8)
   {
      if ($mustContainNumber && preg_match("/\d+/", $value) == false) {
         self::$status = false;
         self::$error[$field] = "$field must contain numbers";
         return false;
      }
      if ($mustContainLowerCase && preg_match("/[a-z]+/", $value) == false) {
         self::$status = false;
         self::$error[$field] = "$field must contain lower-case letters";
         return false;
      }
      if ($mustContainUpperCase && preg_match("/[A-Z]+/", $value) == false) {
         self::$status = false;
         self::$error[$field] = "$field must contain upper-case letters";
         return false;
      }
      if ($mustContainSpecialChars && preg_match("/[!\"#\$%&'()*+,-.\/:;<=>?@[\\]\^_`{|}~]+/", $value) == false) {
         self::$status = false;
         self::$error[$field] = "$field must contain special characters";
         return false;
      }
      if (strlen($value) < $minlength) {
         self::$status = false;
         self::$error[$field] = "$field cannot be less than $minlength characters";
         return false;
      }
      return true;
   }

   // telephone
   public function isValidTelephone(string $field, string $value)
   {
      if (preg_match("/^[0-9-+()]+$/", preg_replace("/\s/", "", $value)) == false) {
         self::$status = false;
         self::$error[$field] = "$field is not a valid telephone number";
         return false;
      }
      return true;
   }

   // date
   public function isValidDate(string $field, string $value)
   {
      if (preg_match("/^[0-9-:\/WT]+$/", $value) == false) {
         self::$status = false;
         self::$error[$field] = "$field is not a valid date";
         return false;
      }
      return true;
   }

   // url
   public static function isValidUrl(string $field, string $value)
   {
      if (filter_var($value, FILTER_VALIDATE_URL, [FILTER_FLAG_PATH_REQUIRED, FILTER_FLAG_QUERY_REQUIRED]) == false) {
         self::$status = false;
         self::$error[$field] = "$field is not a valid url (e.g https://example.com/path?name=john)";
         return false;
      }
      return true;
   }

   // domain
   public function isValidDomain(string $field, string $value)
   {
      if (filter_var($value, FILTER_VALIDATE_DOMAIN, [FILTER_FLAG_HOSTNAME]) == false) {
         self::$status = false;
         self::$error[$field] = "$field is not a valid domain (e.g https://example.com)";
         return false;
      }
      return true;
   }

   // ip
   public static function isValidIP(string $field, string $value)
   {
      if (filter_var($value, FILTER_VALIDATE_IP, [FILTER_FLAG_IPV4, FILTER_FLAG_IPV6, FILTER_FLAG_NO_PRIV_RANGE, FILTER_FLAG_NO_RES_RANGE]) == false) {
         self::$status = false;
         self::$error[$field] = "$field is not a valid ip address (e.g 192.168.0.0)";
         return false;
      }
      return true;
   }

   // mac
   public static function isValidMAC(string $field, string $value)
   {
      if (filter_var($value, FILTER_VALIDATE_MAC) == false) {
         self::$status = false;
         self::$error[$field] = "$field is not a valid mac address (e.g  00:A0:C9:14:C8:29)";
         return false;
      }
      return true;
   }

}
