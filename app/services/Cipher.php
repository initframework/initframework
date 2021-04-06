<?php
namespace Services;

class Cipher
{

   public static function hash(int $length = 7)
   {
      return substr(bin2hex(openssl_random_pseudo_bytes($length)), 0, $length);
   }

   public static function token(int $length = 6)
   {
      $token = "";
      for ($i=0; $i < $length; $i++) { 
         $token .= rand(0, 9);
      }
      return $token;
   }

   public static function encryptDigest(string $username, string $password)
   {
      // md5(username:realm:password)
      return \md5($username . ":" . APP_NAME . ":" . $password);
   }

   public static function decryptDigest(string $authDigest)
   {
      
      // protect against missing data
      $needed_parts = array('nonce'=>1, 'nc'=>1, 'opaque'=>1, 'cnonce'=>1, 'qop'=>1, 'realm'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
      $data = array();
      $keys = implode('|', array_keys($needed_parts));

      preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $authDigest, $matches, PREG_SET_ORDER);

      foreach ($matches as $m) {
         $data[trim($m[1])] = $m[3] ? $m[3] : $m[4];
         unset($needed_parts[$m[1]]);
      }

      return $needed_parts ? [] : $data;

   }

   public static function signJWT(array $data)
   {
      /**
       * Uncomment the following line and add an appropriate date to enable the 
         * "not before" feature.
         */
      $nbf = strtotime('now');

      /**
       * Uncomment the following line and add an appropriate date and time to enable the 
         * "expire" feature.
         */
      $exp = strtotime(AUTH_LIFETIME . " minutes");
      
      if (isset($nbf)) {$data['nbf'] = $nbf;}
      if (isset($exp)) {$data['exp'] = $exp;}
      return JWT::encode($data, APP_KEY, "HS512");
   }

   public static function hashPassword(string $password)
   {
      $options = [
         'cost' => 10
      ];
   
      $password =  password_hash($password, PASSWORD_BCRYPT, $options);
      return $password;
   }

   public static function verifyPassword(string $password, string $hash)
   {
      return password_verify($password, $hash);
   }

   public static function encryptAES($key, $data)
   {
      // prepare key
      $key = base64_decode($key);
      $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

      // encrypt data
      $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
      $ciphertext = base64_encode($encrypted . '::' . $iv);
      return $ciphertext;
   }

   public static function decryptAES($key, $ciphertext)
   {
      // prepare key
      $key = base64_decode($key);

      // decrypt data
      list($encrypted_data, $iv) = explode('::', base64_decode($ciphertext), 2);
      $data = openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
      return $data;
   }

   public static function encryptAESJson($key, $data)
   {
      $salt = openssl_random_pseudo_bytes(8);
      $salted = '';
      $dx = '';
      while (strlen($salted) < 48) {
         $dx = md5($dx.$key.$salt, true);
         $salted .= $dx;
      }
      $key = substr($salted, 0, 32);
      $iv  = substr($salted, 32,16);
      $ciphertext = openssl_encrypt(json_encode($data), 'aes-256-cbc', $key, true, $iv);
      $ciphertext = array("ct" => base64_encode($ciphertext), "iv" => bin2hex($iv), "s" => bin2hex($salt));
      $cipherjson = json_encode($ciphertext);
      return $cipherjson;
   }
   
   public static function decryptAESJson($key, $cipherjson)
   {
		$cipherjson = json_decode($cipherjson, true);
      try {
         $salt = hex2bin($cipherjson["s"]);
         $iv  = hex2bin($cipherjson["iv"]);
      } catch(\Exception $e) { return null; }
      $ct = base64_decode($cipherjson["ct"]);
      $key = $key.$salt;
      $md5 = array();
      $md5[0] = md5($key, true);
      $result = $md5[0];
      for ($i = 1; $i < 3; $i++) {
         $md5[$i] = md5($md5[$i - 1].$key, true);
         $result .= $md5[$i];
      }
      $key = substr($result, 0, 32);
      $ciphertext = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
      $data = json_decode($ciphertext, true);
      return $data;
   }
   

   

}
