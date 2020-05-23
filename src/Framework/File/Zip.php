<?php
namespace Framework\File;
use ZipArchive;
use Framework\Handler\IException;

class Zip //extends ZipArchive
{
   private static $zip;

   public static function zip(string $src, string $dest, string $filename = '')
   {
      try
      {
         $storage_src = STORAGE_DIR . $src;
         $storage_dest = STORAGE_DIR . $dest; echo $storage_dest;
         $filename = $filename == '' ? STORAGE_DIR . self::_hash() . '.zip' : STORAGE_DIR . $filename;

         if (!\is_file($storage_src) && !\is_dir($storage_src)) {
            throw new IException("Error: $src is not a file or a directory in storage!");  
         } else {
            self::$zip = new ZipArchive();

            $storage_src = realpath($storage_src);

            self::$zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            $files = new \RecursiveIteratorIterator(
               new \RecursiveDirectoryIterator($storage_dest),
               \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
               if (!$file->isDir()) {
                  $filePath = $file->getRealPath();
                  $relativePath = substr($filePath, strlen($storage_dest));
                  self::$zip->addFile($filePath, $relativePath);
               }
            }

            self::$zip->close();
         }
      }
      catch (IException $ex) {
         $ex->handle();
      }
   }

   public static function unzip() {}

   private static function _hash() 
   {
      $hex = ['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f'];
      $hash = "";
      for ($max = 7; $max > 0; $max--) {
         $i = rand(0,15);
         $hash .= $hex[$i];
      }
      return $hash;
   }
}