<?php
namespace Services;

class Upload
{

   // Image types
   public static $imagefiles = [
      "image/svg+xml"=>".svg",
      "image/jpeg"=>".jpg",
      "image/png"=>".png",
      "image/gif"=>".gif",
      "image/bmp"=>".bmp",
      "image/vnd.microsoft.icon"=>".ico",
      "image/tiff"=>".tiff",
      "image/webp"=>".webp"
   ];
   // Video types
   public static $videofiles = [
      "video/x-msvideo"=>".avi",
      "video/mpeg"=>".mpeg",
      "video/mp4"=>".mp4",
      "video/ogg"=>".ogv",
      "video/mp2t"=>".ts",
      "video/3gpp"=>".3gp",
      "video/webm"=>".webm",
      "video/3gpp2"=>".3g2"
   ];
   // Audio types
   public static $audiofiles = [
      "audio/aac"=>".aac",
      "audio/wav"=>".wav",
      "audio/ogg"=>".oga",
      "audio/midi"=>".mid",
      "audio/midi"=>".midi",
      "audio/x-midi"=>".mid",
      "audio/x-midi"=>".midi",
      "audio/mp3"=>".mp3",
      "audio/mpeg"=>".mpeg",
      "audio/webm"=>".weba",
      "audio/3gpp"=>".3gp",
      "audio/3gpp2"=>".3gp2"
   ];
   // Zip types
   public static $zipfiles = [
      "application/x-7z-compressed"=>".7z",
      "application/zip"=>".zip",
      "application/x-tar"=>".tar",
      "application/x-rar-compressed"=>".rar",
      "application/java-archive"=>".jar",
      "application/gzip"=>".gz",
      "application/x-freearc"=>".arc",
      "application/x-bzip"=>".bz",
      "application/x-bzip2"=>".bz2"
   ];
   // Document types
   public static $documentfiles = [
      "application/vnd.openxmlformats-officedocument.wordprocessingml.document"=>".docx",
      "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"=>".xlsx",
      "application/vnd.ms-excel"=>".xls",
      "application/vnd.visio"=>".vsd",
      "application/vnd.openxmlformats-officedocument.presentationml.presentation"=>".pptx",
      "application/vnd.ms-powerpoint"=>".ppt",
      "application/vnd.oasis.opendocument.text"=>".odt",
      "application/vnd.oasis.opendocument.spreadsheet"=>".ods",
      "application/vnd.oasis.opendocument.presentation"=>".odp",
      "application/msword"=>".doc",
      "application/x-abiword"=>".abw",
      "application/vnd.apple.installer+xml"=>".mpkg",
      "application/epub+zip"=>".epub",
      "application/x-shockwave-flash"=>".swf",
      "application/vnd.mozilla.xul+xml"=>".xul",
      "application/ogg"=>".ogx",
      "application/vnd.amazon.ebook"=>".azw"
   ];
   // Font types
   public static $fontfiles = [
      "application/vnd.ms-fontobject"=>".eot",
      "font/otf"=>".otf",
      "font/ttf"=>".ttf",
      "font/woff"=>".woff",
      "font/woff2"=>".woff2"
   ];
   // Text types
   public static $textfiles = [
      // "text/html"=>".htm",
      "text/html"=>".html",
      "text/plain"=>".txt",
      "text/css"=>".css",
      "text/csv"=>".csv",
      "text/calendar"=>".ics",
      "text/javascript"=>".js",
      // "text/javascript"=>".mjs",
      "text/xml"=>".xml",
      "application/xhtml+xml"=>".xhtml",
      "appliction/php"=>".php",
      "application/pdf"=>".pdf",
      "application/json"=>".json",
      "application/ld+json"=>".jsonld",
      "application/x-csh"=>".csh",
      "application/x-sh"=>".sh",
      "application/octet-stream"=>".bin"
   ];
   private static $validfiles = [];
   public static $unit = "Kb"; // Mb, Gb
   public static $field = "file";

   public static function tmp(string $filename, string $destination, array $accept, int $minsize = null, int $maxsize = null) : object
   {
      self::$validfiles = array_merge(self::$imagefiles, self::$videofiles, self::$audiofiles, self::$documentfiles, self::$fontfiles, self::$textfiles, self::$zipfiles);
      $upload = new \stdClass();
      $upload->error = [];
      $upload->status = true;
      $upload->path = "";
      
      if (!isset($_FILES[$filename]) || empty($_FILES[$filename])) {
         $upload->status = false;
         $upload->error[] = self::$field . " was not uploaded";
         return $upload;
      }

      if (self::accept($_FILES[$filename]['type'], $accept) == false) {
         $upload->status = false;
         $upload->error[] = self::$field . " must have file type of " . implode(", ", $accept);
      }

      if (self::minsize($_FILES[$filename]['size'], $minsize) == false) {
         $upload->status = false;
         $upload->error[] = self::$field . " size cannot be less than $minsize" . self::$unit;
      }

      if (self::maxsize($_FILES[$filename]['size'], $maxsize) == false) {
         $upload->status = false;
         $upload->error[] = self::$field . " size cannot be more than $maxsize" . self::$unit;
      }

      if ($upload->status == true) {
         $destination = ltrim($destination, "\/");
         $extension = self::$validfiles[$_FILES[$filename]['type']];
         $upload->status = move_uploaded_file($_FILES[$filename]['tmp_name'], STORAGE_DIR . $destination . $extension);
         $upload->path = STORAGE_PATH . $destination . $extension;
      }

      return $upload;
   }

   private static function minsize(int $filesize, int $size = null)
   {
      if ($size == null) return true;
      $size = self::size($size);
      return $filesize > $size;
   }

   private static function maxsize(int $filesize, int $size = null)
   {
      if ($size == null) return true;
      $size = self::size($size);
      return $filesize < $size;
   }

   private static function size(int $size)
   {
      switch (self::$unit) {
         case 'Kb':
            return $size * 1024;
         break;
         case 'Mb':
            return $size * 1024 * 1024;
         break;
         case 'Gb':
            return $size * 1024 * 1024 * 1024;
         break;
         default:
            trigger_error("Invalid file size unit!");
         break;
      }
   }

   private static function accept(string $filetype, array $filetypes)
   {
      return \array_key_exists($filetype, $filetypes);
   }

}