<?php
namespace Library\Cipher;

class AES
{
   
   public static function encrypt($key, $data)
   {
      // prepare key
      $key = base64_decode($key);
      $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

      // encrypt data
      $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
      $ciphertext = base64_encode($encrypted . '::' . $iv);
      return $ciphertext;
   }

   public static function decrypt($key, $ciphertext)
   {
      // prepare key
      $key = base64_decode($key);

      // decrypt data
      list($encrypted_data, $iv) = explode('::', base64_decode($ciphertext), 2);
      $data = openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
      return $data;
   }

	public static function encryptJson($key, $data){
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
   
	public static function decryptJson($key, $cipherjson){
		$cipherjson = json_decode($cipherjson, true);
      try {
         $salt = hex2bin($cipherjson["s"]);
         $iv  = hex2bin($cipherjson["iv"]);
      } catch(Exception $e) { return null; }
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