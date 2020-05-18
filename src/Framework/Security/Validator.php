<?php
namespace Providers;

class Validator
{
   private $errors = [];

   // FILTER_VALIDATE_INT #int
   // FILTER_FLAG_ALLOW_OCTAL
   // FILTER_FLAG_ALLOW_HEX

   // field is needed
   // independent error messaging is required for each option

   // method(string $field, $value, array $options, bool $filters)
   // "field:error message", $value, ["option"=>"error message"], true

   public function int(string $field, $value, array $options = [], bool $allowOctal = false, bool $allowHex = false)
   {
      $flags = [];
      // if $field came with error message get it, else assign it a default message
      list($field,$errMsg) = preg_match("/:/",$field) ? [explode(":",$field)[0],explode(":",$field)[1]] : [$field,"Invalid $field field."] ;

      if ($allowOctal == true) { $flags[] = FILTER_FLAG_ALLOW_OCTAL; }
      if ($allowHex == true) { $flags[] = FILTER_FLAG_ALLOW_HEX; }
      
      // if validation returns false
      if (filter_var($value, FILTER_VALIDATE_INT, $flags) == false) {
         // add error message
         $this->errors[$field][] = preg_match("/:/",$field) ? explode(":",$field)[1] : "Invalid $field field." ;
      } // then no need to validate with the options

      // validate with the options
      $this->validateOptions($field, $value, $options);
   }

   public function string(string $field, $value, array $options = [])
   {
      // if $field came with error message get it, else assign it a default message
      list($field,$errMsg) = preg_match("/:/",$field) ? [explode(":",$field)[0],explode(":",$field)[1]] : [$field,"Invalid $field field."] ;

      // if validation returns false
      if (is_string($value) == false) {
         // add error message
         $this->errors[$field][] = $errMsg;
      } // then no need to validate with the options

      // validate with the options
      $this->validateOptions($field, $value, $options);
   }

   // FILTER_VALIDATE_EMAIL #email
   // FILTER_FLAG_EMAIL_UNICODE

   public function email(string $field, $value, array $options = [], bool $emailUnicode = false)
   {
      $flags = [];
      // if $field came with error message get it, else assign it a default message
      list($field,$errMsg) = preg_match("/:/",$field) ? [explode(":",$field)[0],explode(":",$field)[1]] : [$field,"Invalid $field field."] ;
      
      if ($emailUnicode == true) { $flags[] = FILTER_FLAG_EMAIL_UNICODE; }
      
      // if validation returns false
      if (filter_var($value, FILTER_VALIDATE_EMAIL, $flags) == false) {
         // add error message
         $this->errors[$field][] = $errMsg;
      } // then no need to validate with the options

      // validate with the options
      $this->validateOptions($field, $value, $options);
   }

   // FILTER_VALIDATE_IP #ip
   // FILTER_FLAG_IPV4
   // FILTER_FLAG_IPV6
   // FILTER_FLAG_NO_PRIV_RANGE
   // FILTER_FLAG_NO_RES_RANGE

   public function ip(string $field, $value, array $options = [], bool $ipv4 = false, bool $ipv6 = false, bool $noPrivRange = false, bool $noResRange = false)
   {
      $flags = [];
      // if $field came with error message get it, else assign it a default message
      list($field,$errMsg) = preg_match("/:/",$field) ? [explode(":",$field)[0],explode(":",$field)[1]] : [$field,"Invalid $field field."] ;
      
      if ($ipv4 == true) { $flags[] = FILTER_FLAG_IPV4; }
      if ($ipv6 == true) { $flags[] = FILTER_FLAG_IPV6; }
      if ($noPrivRange == true) { $flags[] = FILTER_FLAG_NO_PRIV_RANGE; }
      if ($noResRange == true) { $flags[] = FILTER_FLAG_NO_RES_RANGE; }
      
      // if validation returns false
      if (filter_var($value, FILTER_VALIDATE_IP, $flags) == false) {
         // add error message
         $this->errors[$field][] = $errMsg;
      } // then no need to validate with the options

      // validate with the options
      $this->validateOptions($field, $value, $options);
   }

   // FILTER_VALIDATE_URL #url
   // FILTER_FLAG_SCHEME_REQUIRED
   // FILTER_FLAG_HOST_REQUIRED
   // FILTER_FLAG_PATH_REQUIRED
   // FILTER_FLAG_QUERY_REQUIRED

   public function url(string $field, $value, array $options = [], bool $schemeRequired = false, bool $hostRequired = false, bool $pathRequired = false, bool $queryRequired = false)
   {
      $flags = [];
      // if $field came with error message get it, else assign it a default message
      list($field,$errMsg) = preg_match("/:/",$field) ? [explode(":",$field)[0],explode(":",$field)[1]] : [$field,"Invalid $field field."] ;
      
      if ($schemeRequired == true) { $flags[] = FILTER_FLAG_SCHEME_REQUIRED; }
      if ($hostRequired == true) { $flags[] = FILTER_FLAG_HOST_REQUIRED; }
      if ($pathRequired == true) { $flags[] = FILTER_FLAG_PATH_REQUIRED; }
      if ($queryRequired == true) { $flags[] = FILTER_FLAG_QUERY_REQUIRED; }
      
      // if validation returns false
      if (filter_var($value, FILTER_VALIDATE_URL, $flags) == false) {
         // add error message
         $this->errors[$field][] = $errMsg;
      } // then no need to validate with the options

      // validate with the options
      $this->validateOptions($field, $value, $options);
   }

   // FILTER_VALIDATE_DOMAIN #domain
   // FILTER_FLAG_HOSTNAME

   public function domain(string $field, $value, array $options = [], bool $hostname = false)
   {
      $flags = [];
      // if $field came with error message get it, else assign it a default message
      list($field,$errMsg) = preg_match("/:/",$field) ? [explode(":",$field)[0],explode(":",$field)[1]] : [$field,"Invalid $field field."] ;
      
      if ($hostname == true) { $flags[] = FILTER_FLAG_HOSTNAME; }
      
      // if validation returns false
      if (filter_var($value, FILTER_VALIDATE_DOMAIN, $flags) == false) {
         // add error message
         $this->errors[$field][] = $errMsg;
      } // then no need to validate with the options

      // validate with the options
      $this->validateOptions($field, $value, $options);
   }

   // FILTER_VALIDATE_BOOLEAN #boolean
   // FILTER_NULL_ON_FAILURE

   public function boolean(string $field, $value, array $options = [], bool $nullOnFailure = false)
   {
      $flags = [];
      // if $field came with error message get it, else assign it a default message
      list($field,$errMsg) = preg_match("/:/",$field) ? [explode(":",$field)[0],explode(":",$field)[1]] : [$field,"Invalid $field field."] ;
      
      if ($nullOnFailure == true) { $flags[] = FILTER_NULL_ON_FAILURE; }
      
      // if validation returns false
      if (filter_var($value, FILTER_VALIDATE_BOOLEAN, $flags) == false) {
         // add error message
         $this->errors[$field][] = $errMsg;
      } // then no need to validate with the options

      // validate with the options
      $this->validateOptions($field, $value, $options);
   }

   // FILTER_VALIDATE_FLOAT #float
   // FILTER_FLAG_ALLOW_THOUSAND

   public function float(string $field, $value, array $options = [], bool $allowThousand = false)
   {
      $flags = [];
      // if $field came with error message get it, else assign it a default message
      list($field,$errMsg) = preg_match("/:/",$field) ? [explode(":",$field)[0],explode(":",$field)[1]] : [$field,"Invalid $field field."] ;
      
      if ($allowThousand == true) { $flags[] = FILTER_FLAG_ALLOW_THOUSAND; }
      
      // if validation returns false
      if (filter_var($value, FILTER_VALIDATE_FLOAT, $flags) == false) {
         // add error message
         $this->errors[$field][] = $errMsg;
      } // then no need to validate with the options

      // validate with the options
      $this->validateOptions($field, $value, $options);
   }

   // FILTER_VALIDATE_MAC #mac

   public function mac(string $field, $value, array $options = [])
   {
      // if $field came with error message get it, else assign it a default message
      list($field,$errMsg) = preg_match("/:/",$field) ? [explode(":",$field)[0],explode(":",$field)[1]] : [$field,"Invalid $field field."] ;
      
      // if validation returns false
      if (filter_var($value, FILTER_VALIDATE_MAC) == false) {
         // add error message
         $this->errors[$field][] = $errMsg;
      } // then no need to validate with the options

      // validate with the options
      $this->validateOptions($field, $value, $options);
   }

   // FILTER_VALIDATE_REGEXP #regexp

   public function regexp(string $field, $value, string $pattern ,array $options = [])
   {
      // if $field came with error message get it, else assign it a default message
      list($field,$errMsg) = preg_match("/:/",$field) ? [explode(":",$field)[0],explode(":",$field)[1]] : [$field,"Invalid $field field."] ;
      
      // if validation returns false
      if (filter_var($value, FILTER_VALIDATE_REGEXP, $pattern) == false) {
         // add error message
         $this->errors[$field][] = $errMsg;
      }

      // validate with the options
      $this->validateOptions($field, $value, $options);
   }

   
   /**
    * A method that validates the options
    * 
    * @param       $value is the value to be validated
    * @param array $options is an array of all the options to test the value with
    * @return bool $status is a bool that determines if all conditions were met
    */
   private function validateOptions(string $field, $value, array $options = []) : bool
   {
      
      // for each option met, call its function and return its value to status
      foreach ($options as $opKey => $errMsg) { 
         // define the required variable
         $required = true; $tel = true;
         // if error message wasn't sent
         if (is_int($opKey)) {
            $opKey = $errMsg;
            $errMsg = "";
         }
         
         // find the opKey and the opValue if a colon delimeter is found, else get only the opKey
         list($opKey,$opValue) = preg_match("/:/",$opKey) ? [explode(":",$opKey)[0],(int)explode(":",$opKey)[1]] : [$opKey,null] ;

         switch ($opKey) {
            case 'required':
               $required = $this->required($field, $value, $errMsg);
               break;
            case 'telephone':
               $tel = $this->telephone($field, $value, $errMsg);
               break;
            case 'min':
               $this->min($field, $value, $opValue, $errMsg);
               break;
            case 'max':
               $this->max($field, $value, $opValue, $errMsg);
               break;
            case 'exact':
               $this->exact($field, $value, $opValue, $errMsg);
               break;
            case 'alphabets':
               $this->alphabets($field, $value, $errMsg);
               break;
            case 'notAlphabets':
               $this->notAlphabets($field, $value, $errMsg);
               break;
            case 'lcAlpha':
               $this->lcAlpha($field, $value, $errMsg);
               break;
            case 'ucAlpha':
               $this->ucAlpha($field, $value, $errMsg);
               break;
            case 'numbers':
               $this->numbers($field, $value, $errMsg);
               break;
            case 'notNumbers':
               $this->notNumbers($field, $value, $errMsg);
               break;
            case 'specialChars':
               $this->specialChars($field, $value, $errMsg);
               break;
            case 'notSpecialChars':
               $this->notSpecialChars($field, $value, $errMsg);
               break;
            case 'date':
               $this->date($field, $value, $errMsg);
               break;
            
            default:
               break;
         }

         // if the field is required and the field is not set, then no need to continue checking other options
         if ($required == false) { return false; }
         if ($tel == false) { return false; }
      }

      // if the code gets here then all the options have been verified, hence return true
      return true;
   }

   // private validation options
   private function required($field, $value, string $errMsg = "")
   {
      $errMsg = ($errMsg == "") ? "$field field is required." : $errMsg ;
      if ( (isset($value) && strlen($value) > 0) == false ){
         $this->errors[$field] = [$errMsg];
         return false;
      }
      return true;
   }

   private function telephone(string $field, $value, string $errMsg = "")
   {
      $errMsg = ($errMsg == "") ? "$field field is not valid." : $errMsg ;
      if ( preg_match("/^[0-9-+()]+$/", preg_replace("/\s/", "", $value)) == false ){
         $this->errors[$field] = [$errMsg];
         return false;
      }
      return true;
   }

   private function exact(string $field, $value, int $len, string $errMsg = "")
   {
      $errMsg = ($errMsg == "") ? "$field field must be $len characters long." : $errMsg ;
      if ( (strlen($value) == $len) == false ){
         $this->errors[$field][] = $errMsg;
      }
   }

   private function min(string $field, $value, int $min, string $errMsg = "")
   {
      $errMsg = ($errMsg == "") ? "$field field requires minimum of $min characters." : $errMsg ;
      if ( (strlen($value) >= $min) == false ){
         $this->errors[$field][] = $errMsg;
      }
   }

   private function max(string $field, $value, int $max, string $errMsg = "")
   {
      $errMsg = ($errMsg == "") ? "$field field requires maximum of $max characters." : $errMsg ;
      if ( (strlen($value) <= $max) == false ){
         $this->errors[$field][] = $errMsg;
      }
   }
   
   private function alphabets(string $field, $value, string $errMsg = "")
   {
      $errMsg = ($errMsg == "") ? "$field field must contain alphabets." : $errMsg ;
      if ( preg_match("/^(?=.*[A-Za-z]).*$/", $value) == false ){
         $this->errors[$field][] = $errMsg;
      }
   }

   private function notAlphabets(string $field, $value, string $errMsg = "")
   {
      $errMsg = ($errMsg == "") ? "$field field must not contain alphabets." : $errMsg ;
      if ( preg_match("/^(?=.*[A-Za-z]).*$/", $value) == true ){
         $this->errors[$field][] = $errMsg;
      }
   }

   private function ucAlpha(string $field, $value, string $errMsg = "")
   {
      $errMsg = ($errMsg == "") ? "$field field must contain an uppercase character." : $errMsg ;
      if ( preg_match("/^(?=.*[A-Z]).*$/", $value) == false ){
         $this->errors[$field][] = $errMsg;
      }
   }

   private function lcAlpha(string $field, $value, string $errMsg = "")
   {
      $errMsg = ($errMsg == "") ? "$field field must contain a lowecase character." : $errMsg ;
      if ( preg_match("/^(?=.*[a-z]).*$/", $value) == false ){
         $this->errors[$field][] = $errMsg;
      }
   }

   private function numbers(string $field, $value, string $errMsg = "")
   {
      $errMsg = ($errMsg == "") ? "$field field must contain numbers." : $errMsg ;
      if ( preg_match("/^(?=.*\d).*$/", $value) == false ){
         $this->errors[$field][] = $errMsg;
      }
   }

   private function notNumbers(string $field, $value, string $errMsg = "")
   {
      $errMsg = ($errMsg == "") ? "$field field must not contain numbers." : $errMsg ;
      if ( preg_match("/^(?=.*\d).*$/", $value) == true ){
         $this->errors[$field][] = $errMsg;
      }
   }

   private function specialChars(string $field, $value, string $errMsg = "")
   {
      $errMsg = ($errMsg == "") ? "$field field must contain special characters." : $errMsg ;
      if ( preg_match("/^(?=.*[_.+!*$'(),{}|\\^~\[\]`<>#%\";\/?:@&=-]).*$/", $value) == false ){
         $this->errors[$field][] = $errMsg;
      }
   }

   private function notSpecialChars(string $field, $value, string $errMsg = "")
   {
      $errMsg = ($errMsg == "") ? "$field field must not contain special characters." : $errMsg ;
      if ( preg_match("/^(?=.*[_.+!*$'(),{}|\\^~\[\]`<>#%\";\/?:@&=-]).*$/", $value) == true ){
         $this->errors[$field][] = $errMsg;
      }
   }

   private function date(string $field, $value, string $errMsg = "")
   {
      $errMsg = ($errMsg == "") ? "$field field is an invalid date." : $errMsg ;
      if ( preg_match("/^[0-9-:\/WT]+$/", $value) == false ){
         $this->errors[$field][] = $errMsg;
      }
   }


   // Errors
   public function hasError() : bool
   {
      return (count($this->errors) > 0) ? true : false ;
   }

   public function getFirstError() : string
   {
      $error;
      if ($this->hasError() == true){
         foreach ($this->errors as $field => $errors) {
            $error[] = $errors[0];
         }
      } else { $error = []; }

      return $error[0];
   }

   public function getLastError() : string
   {
      $error;
      if ($this->hasError() == true){
         foreach ($this->errors as $field => $errors) {
            $error[] = $errors[count($errors) - 1];
         }
      } else { $error = []; }

      return $error[count($error) - 1];
   }

   public function getFieldErrors(string $field) : array
   {
      if ($this->hasError() == true){
         return isset($this->errors[$field]) ? $this->errors[$field] : [] ;
      } else { return []; }
   }

   public function getFieldFirstError(string $field) : array
   {
      if ($this->hasError() == true){
         return isset($this->errors[$field][0]) ? [$this->errors[$field][0]] : [] ;
      } else { return []; }
   }

   public function getFieldLastError(string $field) : array
   {
      if ($this->hasError() == true){
         return isset($this->errors[$field][count($this->errors[$field]) - 1]) ? [$this->errors[$field][count($this->errors[$field]) - 1]] : [] ;
      } else { return []; }
   }

   public function getErrors() : array
   {
      if ($this->hasError() == true){
         $errors = [];
         foreach ($this->errors as $field => $errmsgs) {
            foreach ($errmsgs as $error) {
               $errors[] = $error;
            }
         }
         return $errors;
      } else { return []; }
   }

   public function getErrorsByFields() : array
   {
      if ($this->hasError() == true){
         return $this->errors;
      } else { return []; }
   }

   public function getAllFieldsFirstError() : array
   {
      $error;
      if ($this->hasError() == true){
         foreach ($this->errors as $field => $errors) {
            $error[$field] = $errors[0];
         }
      } else { $error = []; }

      return $error;
   }

   public function getAllFieldsLastError() : array
   {
      $error;
      if ($this->hasError() == true){
         foreach ($this->errors as $field => $errors) {
            $error[$field] = $errors[count($errors) - 1];
         }
      } else { $error = []; }

      return $error;
   }

}

?>